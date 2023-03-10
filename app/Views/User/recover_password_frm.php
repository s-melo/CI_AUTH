<?= $this->extend('layouts/User/user_layout') ?>
<?= $this->section('content') ?>

<div class="container vh-100">
    <div class="row vh-100 justify-content-center align-items-center">
        <div class="col-sm-10 col-lg-6 col-xl-5">

            <div class="card p-5">

                <h3 class="text-center">Recover password</h3>
                <hr>

                <!-- form -->

                <?= form_open('user_recover_password_submit') ?>

                <div class="mb-3">
                    <label for="text_username" class="form-label">Forgot your password? What is your username?</label>
                    <input type="email" name="text_username" id="text_username" class="form-control" placeholder="Email" required value="<?= old('text_username') ?>">
                    <?php if (!empty($validation_errors)) : ?>
                        <span class="text-danger fst-italic"><small><?= show_form_errors('text_username', $validation_errors) ?></small></span>
                    <?php endif; ?>
                    <?php if (!empty($server_error)) : ?>
                        <span class="text-danger fst-italic"><small><?= show_form_errors('text_username', $server_error) ?></small></span>
                    <?php endif; ?>
                </div> 

                <div class="mt-5 mb-3 text-center">
                    <a href="<?= site_url('/') ?>" class="btn btn-primary">Cancel</a>
                    <input type="submit" value="Recover" class="btn btn-primary">
                </div>

                <!-- close form -->

                <?= form_close() ?>
                

            </div>

        </div>
    </div>
</div>
<?= $this->endSection() ?>