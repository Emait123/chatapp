<div class="d-flex flex-column flex-shrink-0 p-3 bg-light" style="width: 280px;">
    <a href="/" class="d-flex align-items-center mb-3 mb-md-0 me-md-auto link-dark text-decoration-none">
        <i class="fa fa-hashtag me-2"></i><span class="fs-4">ChatBot</span>
    </a>
    <hr>
    <ul class="nav nav-pills flex-column mb-auto">
        <li class="nav-item">
            <a href="<?= url_to('chat') ?>" class="nav-link <?= ($active == 'home') ? 'active' : 'link-dark' ?>" aria-current="page">
                <i class="fa fa-tachometer-alt me-2"></i> Trang chủ
            </a>
        </li>
        <li>
            <a href="<?= url_to('chat') ?>" class="nav-link <?= ($active == 'chat') ? 'active' : 'link-dark' ?>">
                <i class="fa-solid fa-comments"></i> Chat
            </a>
        </li>
        <li>
            <a href="<?= url_to('timeofflist') ?>" class="nav-link <?= ($active == 'list') ? 'active' : 'link-dark' ?>">
                <i class="fa-solid fa-calendar-days"></i> Thông tin nghỉ phép
            </a>
        </li>
    </ul>
    <hr>
    <div class="dropdown">
        <a href="#" class="d-flex align-items-center link-dark text-decoration-none dropdown-toggle" id="dropdownUser2" data-bs-toggle="dropdown" aria-expanded="false">
            <img src="https://github.com/mdo.png" alt="" width="32" height="32" class="rounded-circle me-2">
            <strong><?= $user['username'] ?></strong>
        </a>
        <ul class="dropdown-menu text-small shadow" aria-labelledby="dropdownUser2">
            <li><a class="dropdown-item" href="#">New project...</a></li>
            <li><a class="dropdown-item" href="#">Settings</a></li>
            <li><a class="dropdown-item" href="#">Profile</a></li>
            <li><hr class="dropdown-divider"></li>
            <li><a class="dropdown-item" href="<?= url_to('logout') ?>">Đăng xuất</a></li>
        </ul>
    </div>
</div>