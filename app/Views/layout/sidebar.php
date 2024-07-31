<div class="sidebar me-4">
    <nav class="navbar bg-light navbar-light d-flex flex-column align-items-start h-100">
        <div>
            <a href="#" class="navbar-brand mx-4 mb-3"><h3 class="text-primary"><i class="fa fa-hashtag me-2"></i>ChatBot</h3></a>
            <div class="d-flex align-items-center ms-4 mb-4">
                <div class="ms-3">
                    <h6 class="mb-0"><?= $user['name'] ?></h6>
                    <span><?= $user['role_name'] ?></span>
                </div>
            </div>
        </div>
        <div class="navbar-nav w-100">
            <a href="<?= url_to('chat') ?>" class="nav-item nav-link"><i class="fa fa-tachometer-alt me-2"></i> Trang chủ</a>
            <a href="<?= url_to('chat') ?>" class="nav-item nav-link"><i class="fa-solid fa-comments"></i> Chat</a>
            <a href="<?= url_to('timeofflist') ?>" class="nav-item nav-link "><i class="fa-solid fa-calendar-days"></i> Thông tin nghỉ phép</a>
        </div>
        <div class="navbar-nav align-self-end w-100">
            <hr/>
            <a href="<?= url_to('logout') ?>" class="nav-item nav-link"><i class="fa-solid fa-arrow-right-from-bracket"></i> Đăng xuất</a>
        </div>
    </nav>
</div>