<!doctype html>
<html>

	<head>
		<title>KnowItAll</title>
        <link rel="stylesheet" href="<?= base_url('assets/custom/css/index.css') ?>">
        <script src="<?= base_url('assets/custom/js/index.js') ?>" defer></script>
		<link rel="preconnect" href="https://fonts.googleapis.com">
		<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
		<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700&display=swap" rel="stylesheet">
		<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
		<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
	</head>

	<body>
		<main>
			<div class="container chatbot-container">
				<div class="row">
					<div class="col-12">
						<div class="chatbot-conversation-container" id="chatbot-conversation">
							<div class="speech speech-ai">
								Tôi có thể giúp gì bạn?
							</div>
						</div>
						<form id="form" class="chatbot-input-container">
							<input name="user-input" type="text" id="user-input" required>
							<button id="submit-btn" class="submit-btn">
								<i class="fa-solid fa-paper-plane" style="color: #ffffff;"></i>
							</button>
						</form>
					</div>
				</div>
			</div>
		</main>
	</body>

</html>