<?php

namespace App\Controllers\User;

use App\Controllers\BaseController;
use App\Models\User\UserModel;

class UserController extends BaseController
{
    public function index()
    {
        
    }

    // =====================================================================
    // LOGIN
    // =====================================================================
    public function login_frm()
    {
        // check if there are validation errors in session
        $data = [];
        if (session()->has('validation_errors')) {
            $data['validation_errors'] = session()->getFlashdata('validation_errors');
        }

        // server validation error
        if(session()->has('server_error')) {
            $data['server_error'] = session()->getFlashdata('server_error');
        }

        echo view('user/login_frm', $data);
    }

    // =====================================================================
    public function login_submit()
    {

        if ($this->request->getMethod() != 'post') {
            return redirect()->to('/');
        }

        // form validation
        $validation = $this->validate([
            'text_username' => [
                'label' => 'Username',
                'rules' => 'required|valid_email',
                'errors' => [
                    'required' => 'O campo {field} é de preenchimento obrigatório.',
                    'valid_email' => 'O campo {field} tem de ser um email válido.'
                ]
            ],
            'text_passwrd' => [
                'label' => 'Password',
                'rules' => 'required|min_length[6]|max_length[18]|regex_match[/(?=.*\d)(?=.*[a-z])(?=.*[A-Z])/]',
                'errors' => [
                    'required' => 'O campo {field} é de preenchimento obrigatório.',
                    'min_length' => 'O campo {field} tem de ter no mínimo, {param} caracteres.',
                    'max_length' => 'O campo {field} tem de ter no máximo, {param} caracteres.',
                    'regex_match' => 'A password tem de ter uma maiúscula, uma minúscula e um dígito.'
                ]
            ]
        ]);

        // Check if validation has failed
        if (!$validation) {
            return redirect()
                ->back()
                ->withInput()
                ->with('validation_errors', $this->validator->getErrors());
        }

        $users = new UserModel();

        $username = $this->request->getPost('text_username');
        $passwrd = $this->request->getPost('text_passwrd');

        // check if login is ok
        $results = $users->verify_login($username, $passwrd);

        if (!$results['status']) {
            
            // login error
            // die('Erro de login');            
                return redirect()
                    ->back()
                    ->withInput()
                    ->with('server_error', 'Username ou password incorreto.');
            

        }

        // get user available data

        $id_user =  $results['id_user'];
        $results = $users->get_user_data($id_user);

        // create user session
        session()->set('user', $results[0]);

        return redirect('/');
    }

    // =====================================================================
    public function logout()
    {
        // logout
        session()->remove('user');
        return redirect()->to('/');
    }


    // =====================================================================
    // NEW USER ACCOUNT
    // =====================================================================
    public function new_user_account_frm()
    {
        // check if there are validation errors in session
        $data = [];
        if (session()->has('validation_errors')) {
            $data['validation_errors'] = session()->getFlashdata('validation_errors');
        }

        echo view('user/new_user_account_frm', $data);
    }

    // =====================================================================
    // =====================================================================
    public function new_user_account_submit()
    {
        if ($this->request->getMethod() != 'post') {
            return redirect()->to('/');
        }

        // form validation
        $validation = $this->validate([
            'text_username' => [
                'label' => 'Username',
                'rules' => 'required|valid_email',
                'errors' => [
                    'required' => 'O campo {field} é de preenchimento obrigatório.',
                    'valid_email' => 'O campo {field} tem de ser um email válido.'
                ]
            ],
            'text_passwrd' => [
                'label' => 'Password',
                'rules' => 'required|min_length[6]|max_length[18]|regex_match[/(?=.*\d)(?=.*[a-z])(?=.*[A-Z])/]',
                'errors' => [
                    'required' => 'O campo {field} é de preenchimento obrigatório.',
                    'min_length' => 'O campo {field} tem de ter no mínimo, {param} caracteres.',
                    'max_length' => 'O campo {field} tem de ter no máximo, {param} caracteres.',
                    'regex_match' => 'A password tem de ter uma maiúscula, uma minúscula e um dígito.'
                ]
            ],
            'text_repeat_passwrd' => [
                'label' => 'Repetir Password',
                'rules' => 'required|min_length[6]|max_length[18]|regex_match[/(?=.*\d)(?=.*[a-z])(?=.*[A-Z])/]|matches[text_passwrd]',
                'errors' => [
                    'required' => 'O campo {field} é de preenchimento obrigatório.',
                    'min_length' => 'O campo {field} tem de ter no mínimo, {param} caracteres.',
                    'max_length' => 'O campo {field} tem de ter no máximo, {param} caracteres.',
                    'regex_match' => 'A password tem de ter uma maiúscula, uma minúscula e um dígito.',
                    'matches' => 'A repetição da password deve de ser igual à password.'
                ]
            ]
        ]);

        // Check if validation has failed
        if (!$validation) {
            return redirect()
                ->back()
                ->withInput()
                ->with('validation_errors', $this->validator->getErrors());
        }


        // check if username alredy exists in the database
        $user = new UserModel();
        $result = $user->check_if_user_account_already_exists($this->request->getPost('text_username'));

        if($result){
            die('já existe uma conta com o mesmo email.');
        }

        // create new user account
        $username = $this->request->getPost('text_username');
        $passwrd = $this->request->getPost('text_passwrd');
        $purl_code = $user->create_new_user_account($username, $passwrd);
        

        // send email with purl to validate email address
        $purl = site_url('verify_email/' . $purl_code);

        $email = \Config\Services::email();
        $email->setFrom('ci_auth@ci_auth.com', 'CI Auth');
        $email->setTo($username);

        $email->setSubject('CI Auth - Confirmação de email');
        $email->setMailType('html');

        $message = 
        '
        <h3>CI - Auth - Confirmação de conta de usuário</h3>
        <p>Para concluir o seu registo, clique no link abaixo.</p>
        <p>
        <a href="' . $purl . '">Confirmar email</a>
        </p>
        ';

        $email->setMessage($message);

        $email->send();

        // apresentar mensagem...
        // die('Foi enviado um email para ' . $username);
        
        $this->show_confirmation_email($username);       

    }

    // =====================================================================
    private function show_confirmation_email($username)
    {
        $data['email'] = $username;
        echo view('user/show_confirmation_email', $data);
    }

    // =====================================================================
    public function verify_email($purl = null)
    {
        // check if purl is valid
        if(empty($purl) || strlen($purl) != 12){
            return redirect()->to('/');
        }

        $user = new UserModel();
        $results = $user->check_confirm_email_address($purl);

        // purl is invalid
        if(!$results){
            return redirect()->to('/');
        }

        // purl is ok.
        // Account confirmation success
        echo view('user/show_confirmation_final_message');
        
    }

    
    
    
    
    // =====================================================================
    // recover password
    // =====================================================================
    public function recover_password_frm()
    {
        // check if there are validation errors in session
        $data = [];

        // form validation error
        if (session()->has('validation_errors')) {
            $data['validation_errors'] = session()->getFlashdata('validation_errors');
        }

        // server validation error
        if (session()->has('server_error')) {
            $data['server_error'] = session()->getFlashdata('server_error');
        }
        
        return view('user/recover_password_frm', $data);
    }

    // =====================================================================
    public function recover_password_submit()
    {
        if ($this->request->getMethod() != 'post') {
            return redirect()->to('/');
        }

        // form validation
        $validation = $this->validate([
            'text_username' => [
                'label' => 'Username',
                'rules' => 'required|valid_email',
                'errors' => [
                    'required' => 'O campo {field} é de preenchimento obrigatório.',
                    'valid_email' => 'O campo {field} tem de ser um email válido.'
                ]
            ]
        ]);

        // Check if validation has failed
        if (!$validation) {
            return redirect()
                ->back()
                ->withInput()
                ->with('validation_errors', $this->validator->getErrors());
        }


        // check if the username exists in the database
        $username = $this->request->getPost("text_username");
        $user = new UserModel();
        $results = $user->check_if_user_can_recover_password($username);

        
        // check results
        if(!$results['status']){
            return redirect()
                ->back()
                ->withInput()
                ->with('server_error', ['text_username' => 'O usuário ' . $username . ' não está registado na aplicação.']);
        }

        // printData($results);

        // initiate user recover password
        $purl_code = $user->set_user_recover_password($results['id_user'])['purl'];
        $purl = site_url('user_recover_password_check/' . $purl_code);

        $email = \Config\Services::email();
        $email->setFrom('ci_auth@ci_auth.com', 'CI Auth');
        $email->setTo($username);

        $email->setSubject('CI Auth - Recuperação de password');
        $email->setMailType('html');

        $message = 
        '
        <h3>CI - Auth - Recuperação de password</h3>
        <p>Para concluir o processo de recuperação de password, clique no link abaixo.</p>
        <p>
        <a href="' . $purl . '">Recuperar a password</a>
        </p>
        ';

        $email->setMessage($message);

        $email->send();
        
        // display message about recover password email sent to the user
        $data['username'] = $username;
        return view('user/recover_password_email_sent', $data);

    }


    // =====================================================================
    public function recover_password_check($purl = null)
    {
        if(empty($purl) || strlen($purl) != 12){
            return redirect()->to("/");
        }

        // check if purl is valid in the database
        $user = new UserModel();
        $results = $user->check_if_purl_exists_reset_password($purl);

        if($results['status'] == 'error'){
            return redirect()->to("/user_recover_password_invalid_purl");
        }

        $data['id_user'] = $results['id_user'];
        
        // check if there are validation errors
        if (session()->has('validation_errors')) {
            $data['validation_errors'] = session()->getFlashdata('validation_errors');
        }

        return view('user/recover_password_define_password_frm', $data);

        /*
        - verificar se o purl está associado a uma conta de utilizador
            -> não - vai para a home page
            -> sim - apresenta página com formulário para definir nova password
        */

    }


    // =====================================================================
    public function recover_password_invalid_purl()
    {
        // display a page with the information that purl code is invalid (or expired)
        return view('user/recover_password_invalid_purl');
    }

    
    // =====================================================================
    public function user_recover_password_define_submit()
    {
        // change user password after recover

        // check if id user exists and is valid
        $id_user = null;
        if(!empty($this->request->getPost('id_user'))){
            $id_user = aes_decrypt($this->request->getPost('id_user'));
            if(is_bool($id_user) && !$id_user){
                return redirect()->to("/");
            }
        }

        // form validation
        $validation = $this->validate([
            'text_passwrd' => [
                'label' => 'Password',
                'rules' => 'required|min_length[6]|max_length[18]|regex_match[/(?=.*\d)(?=.*[a-z])(?=.*[A-Z])/]',
                'errors' => [
                    'required' => 'O campo {field} é de preenchimento obrigatório.',
                    'min_length' => 'O campo {field} tem de ter no mínimo, {param} caracteres.',
                    'max_length' => 'O campo {field} tem de ter no máximo, {param} caracteres.',
                    'regex_match' => 'A password tem de ter uma maiúscula, uma minúscula e um dígito.'
                ]
            ],
            'text_repeat_passwrd' => [
                'label' => 'Repetir password',
                'rules' => 'required|min_length[6]|max_length[18]|regex_match[/(?=.*\d)(?=.*[a-z])(?=.*[A-Z])/]|matches[text_passwrd]',
                'errors' => [
                    'required' => 'O campo {field} é de preenchimento obrigatório.',
                    'min_length' => 'O campo {field} tem de ter no mínimo, {param} caracteres.',
                    'max_length' => 'O campo {field} tem de ter no máximo, {param} caracteres.',
                    'regex_match' => 'A password tem de ter uma maiúscula, uma minúscula e um dígito.',
                    'matches' => 'As passwords não são iguais.'
                ]
            ]
        ]);

        // Check if validation has failed
        if (!$validation) {
            return redirect()
                ->back()
                ->withInput()
                ->with('validation_errors', $this->validator->getErrors());
        }

        // check user's password
        $users = new UserModel();
        $results = $users->update_users_password($id_user, $this->request->getPost('text_passwrd'));
        
        if($results['status'] == 'success'){
            return view('user/recover_password_define_password_success');
        }        
        
    }
    
    // =====================================================================

    public function versessao()
    {
        echo '<pre>';
        print_r(session()->get());
    }
}
