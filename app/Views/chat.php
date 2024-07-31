<?= $this->extend('layout/main.php') ?>

<?= $this->section('head_lib') ?>
    <link rel="stylesheet" href="<?= base_url('assets/custom/css/index.css') ?>">
    <script src="<?= base_url('assets/custom/js/index.js') ?>" defer></script>
<?= $this->endSection('head_lib') ?>

<?= $this->section('sidebar') ?>
    <?= $this->include('layout/sidebar.php', $user) ?>
<?= $this->endSection('sidebar') ?>

<?= $this->section('content') ?>
<main class="w-100">
    <div class="container chatbot-container h-90">
        <div class="row">
            <div class="col-12">
                <div class="chatbot-conversation-container" id="chatbot-conversation">
                    <div class="speech speech-ai">Tôi có thể giúp gì bạn?</div>
                </div>
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
</main>
<?= $this->endSection('content') ?>