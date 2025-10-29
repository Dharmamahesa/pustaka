<div class="container">

    <div class="row justify-content-center">

        <div class="col-xl-10 col-lg-12 col-md-9"> 

            <div class="card o-hidden border-0 shadow-lg my-5">
                <div class="card-body p-0">
                    <div class="row">
                        
                        <div class="col-lg-6 d-none d-lg-block bg-login-image" style="
                            background: url('<?= base_url('assets/img/login-bg.jpg'); ?>'); 
                            background-position: center;
                            background-size: cover;">
                        </div>
                        
                        <div class="col-lg-6">
                            <div class="p-5">
                                <div class="text-center">
                                    <h1 class="h4 text-gray-900 mb-4">Selamat Datang di Pustaka Booking!</h1>
                                </div>
                                
                                <?= session()->getFlashdata('pesan'); ?>

                                <form class="user" method="post" action="<?= base_url('autentifikasi'); ?>">
                                    
                                    <div class="form-group">
                                        <input type="text" 
                                               class="form-control form-control-user" 
                                               value="<?= old('email'); ?>" 
                                               id="email" 
                                               placeholder="Masukkan Alamat Email" 
                                               name="email">
                                        <div class="text-danger pl-3" style="font-size: small;">
                                            <?= (isset($validation) ? $validation->getError('email') : ''); ?>
                                        </div>
                                    </div>
                                    
                                    <div class="form-group">
                                        <input type="password" 
                                               class="form-control form-control-user" 
                                               id="password" 
                                               placeholder="Password" 
                                               name="password">
                                        <div class="text-danger pl-3" style="font-size: small;">
                                            <?= (isset($validation) ? $validation->getError('password') : ''); ?>
                                        </div>
                                    </div>
                                    
                                    <button type="submit" class="btn btn-primary btn-user btn-block">
                                        <i class="fas fa-sign-in-alt"></i> MASUK SEKARANG
                                    </button>
                                </form>
                                
                                <hr>
                                
                                <div class="text-center">
                                    <a class="small d-block mb-1" href="<?= base_url('autentifikasi/lupaPassword'); ?>">Lupa Password?</a>
                                    <a class="small d-block" href="<?= base_url('autentifikasi/registrasi'); ?>">Belum Punya Akun? **Daftar Disini!**</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

    </div>

</div>