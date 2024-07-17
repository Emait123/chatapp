<!doctype html>
<html>

	<head>
		<title>KnowItAll</title>
        <link rel="stylesheet" href="<?= base_url('assets/custom/css/index.css') ?>">
        <script src="<?= base_url('assets/custom/js/index.js') ?>" defer></script>
		<link rel="preconnect" href="https://fonts.googleapis.com">
		<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
		<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700&display=swap" rel="stylesheet">
	</head>

	<body>
		<main>
			<section class="chatbot-container">
				<div class="chatbot-header">
					<img src="images/owl-logo.png" class="logo">
					<h1>KnowItAll</h1>
					<h2>Ask me anything!</h2>
					<p class="supportId">User ID: 2344</p>
				</div>
				<div class="chatbot-conversation-container" id="chatbot-conversation">
					<div class="speech speech-ai">
						How can I help you?
					</div>
				</div>
				<form id="form" class="chatbot-input-container">
					<input name="user-input" type="text" id="user-input" required>
					<button id="submit-btn" class="submit-btn">
						<img 
							src="images/send-btn-icon.png" 
							class="send-btn-icon"
						>
					</button>
				</form>
			</section>
		</main>
	</body>

</html>