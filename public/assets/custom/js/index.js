const chatbotConversation = document.getElementById('chatbot-conversation')
const context = []
const mode = 'question'

document.addEventListener('submit', (e) => {
    e.preventDefault()

    if (context.length >= 10) {
        alert('Số lượng câu hỏi-đáp đã quá 5 câu. Tải lại trang để reset lại thông tin');
        return;
    }
    
    const userInput = document.getElementById('user-input')
    let question = userInput.value
    const newSpeechBubble = document.createElement('div')
    newSpeechBubble.classList.add('speech', 'speech-human')
    chatbotConversation.appendChild(newSpeechBubble)
    newSpeechBubble.textContent = userInput.value
    userInput.value = ''

    let pending = document.createElement('div')
    pending.classList.add('speech', 'speech-ai')
    pending.setAttribute('id', 'pending')
    pending.innerHTML = '<div class="spinner-border" role="status"><span class="visually-hidden">Loading...</span></div>'
    chatbotConversation.appendChild(pending)
    chatbotConversation.scrollTop = chatbotConversation.scrollHeight
    
    //Gửi request đến server để lấy thông tin
    let formData = new FormData();
    formData.append('action', 'user-input');
    formData.append('question', question);
    formData.append('context', context);
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
            //lấy được thông tin xong mới push question vào context
            context.push(question)
            console.log(context)
            document.getElementById('pending').remove()
            let answer = document.createElement('div')
            answer.classList.add('speech', 'speech-ai')
            answer.textContent = data['content']
            chatbotConversation.appendChild(answer)
            chatbotConversation.scrollTop = chatbotConversation.scrollHeight
            // context.push('assistant', data['content'])
            context.push(data['content'])
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