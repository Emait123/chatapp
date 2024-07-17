const chatbotConversation = document.getElementById('chatbot-conversation')

document.addEventListener('submit', (e) => {
    e.preventDefault()
    const userInput = document.getElementById('user-input')
    const newSpeechBubble = document.createElement('div')
    newSpeechBubble.classList.add('speech', 'speech-human')
    chatbotConversation.appendChild(newSpeechBubble)
    newSpeechBubble.textContent = userInput.value
    userInput.value = ''
    chatbotConversation.scrollTop = chatbotConversation.scrollHeight

    let formData = new FormData();
    formData.append('action', 'user-input');
    formData.append('question', userInput.value);

    //Gửi request đến server để lấy thông tin
    fetch('home/api', {
        method: 'POST',
        mode: 'no-cors',
        headers: { 'Content-Type': 'application/json' },
        body: formData
    })
    .then(
        (response) => {
        if (response.status !== 200) {
            return console.log('Lỗi, mã lỗi ' + response.status);
        }
        // parse response data
        response.json().then(data => {
            console.log(data)
            return;
        })
        }
    )
    .catch(err => {
        return console.log('Error :-S', err)
    });
})

function renderTypewriterText(text) {
    const newSpeechBubble = document.createElement('div')
    newSpeechBubble.classList.add('speech', 'speech-ai', 'blinking-cursor')
    chatbotConversation.appendChild(newSpeechBubble)
    let i = 0
    const interval = setInterval(() => {
        newSpeechBubble.textContent += text.slice(i-1, i)
        if (text.length === i) {
            clearInterval(interval)
            newSpeechBubble.classList.remove('blinking-cursor')
        }
        i++
        chatbotConversation.scrollTop = chatbotConversation.scrollHeight
    }, 50)
}