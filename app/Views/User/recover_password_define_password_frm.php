<?= $this->extend('layouts/User/user_layout') ?>
<?= $this->section('content') ?>

<div class="container vh-100">
    <div class="row vh-100 justify-content-center align-items-center">
        <div class="col-sm-10 col-lg-6 col-xl-5">

            <div class="card p-5">

                <h3 class="text-center">Redefinir a passwsord</h3>
                <hr>

                <!-- form -->

                <?= form_open('user_recover_password_define_submit') ?>

                <input type="hidden" name="id_user" value="<?= aes_encrypt($id_user) ?>">
                
                <div class="mb-3">
                    <label for="text_passwrd" class="form-label">Password</label>
                    <input type="password" name="text_passwrd" id="text_passwrd" class="form-control" placeholder="Password" required>
                    <?php if (!empty($validation_errors)) : ?>
                        <span class="text-danger fst-italic"><small><?= show_form_errors('text_passwrd', $validation_errors) ?></small></span>
                    <?php endif; ?>
                </div>

                <div class="mb-3">
                    <label for="text_repeat_passwrd" class="form-label">Repetir Password</label>
                    <input type="password" name="text_repeat_passwrd" id="text_repeat_passwrd" class="form-control" placeholder="Repetir Password" required>
                    <?php if (!empty($validation_errors)) : ?>
                        <span class="text-danger fst-italic"><small><?= show_form_errors('text_repeat_passwrd', $validation_errors) ?></small></span>
                    <?php endif; ?>
                </div>                
                
                
                <div class="mb-3 text-center">
                    <a href="<?= site_url('/') ?>" class="btn btn-secondary">Início</a>
                    <button type="submit" class="btn btn-secondary">Alterar</button>   
                </div>

                <!-- close form -->

                <?= form_close() ?>
                

            </div>

        </div>
    </div>
</div>



<?= $this->endSection() ?>