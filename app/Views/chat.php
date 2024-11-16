<?= $this->extend('layout/main.php') ?>

<?= $this->section('head_lib') ?>
    <link rel="stylesheet" href="<?= base_url('assets/custom/css/index.css') ?>">
    <script src="<?= base_url('assets/custom/js/index.js') ?>" defer></script>
<?= $this->endSection('head_lib') ?>

<?= $this->section('sidebar') ?>
    <?= $this->include('layout/sidebar.php', [$user, $active]) ?>
<?= $this->endSection('sidebar') ?>

<?= $this->section('content') ?>
<main class="w-100">
    <div class="container-fluid chatbot-container min-vh-100 w-100">
        <div class="row flex-grow-1">
            <div class="col-12 flex-grow-1 d-flex flex-column">
                <div class="chatbot-conversation-container" id="chatbot-conversation">
                    <div class="speech speech-ai">Tôi có thể giúp gì bạn?</div>
                </div>
                <div class="mt-auto">
                    <form id="form" class="chatbot-input-container">
                        <button type="button" id='clear-session' title="Xóa cuộc trò chuyện">
                            <i class="fa-solid fa-eraser" style="color: #ffffff;"></i>
                        </button>
                        <input name="user-input" type="text" id="user-input" required>
                        <button id="submit-btn" class="submit-btn">
                            <i class="fa-solid fa-paper-plane" style="color: #ffffff;"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</main>
<!-- <div class="row">
    <div class="col-12 chatbot-container">
        <div class="chatbot-conversation-container" id="chatbot-conversation">
            <div class="speech speech-ai">Tôi có thể giúp gì bạn?</div>
        </div>
        <div class="mt-auto">
            <form id="form" class="chatbot-input-container">
                <button type="button" id='clear-session' title="Xóa cuộc trò chuyện">
                    <i class="fa-solid fa-eraser" style="color: #ffffff;"></i>
                </button>
                <input name="user-input" type="text" id="user-input" required>
                <button id="submit-btn" class="submit-btn">
                    <i class="fa-solid fa-paper-plane" style="color: #ffffff;"></i>
                </button>
            </form>
        </div>
    </div>
</div> -->
<?= $this->endSection('content') ?>