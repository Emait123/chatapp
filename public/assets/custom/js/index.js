const chatbotConversation = document.getElementById('chatbot-conversation')
const context = []
const mode = 'question'

document.addEventListener('submit', (e) => {
    e.preventDefault()

    if (context.length >= 16) {
        alert('Số lượng câu hỏi-đáp đã quá 8 câu. Tải lại trang để reset lại thông tin');
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
                context.push("user:" + question)
                console.log(context)
                document.getElementById('pending').remove()

                let answer = document.createElement('div')
                answer.classList.add('speech', 'speech-ai')
                answer.textContent = data['content']
                chatbotConversation.appendChild(answer)
                if (data['type'] == 'check') {
                    let check = document.createElement('div')
                    check.classList.add('speech', 'speech-ai', 'd-flex')
                    check.setAttribute('id', 'check')
                    check.innerHTML = "<button type='button' class='btn btn-success confirm-check' title='Đồng ý' data-check='1'><i class='fa-solid fa-check'></i></button><button type='button' class='btn btn-danger confirm-check' title='Không đồng ý' data-check='0'><i class='fa-solid fa-x'></i></button>"
                    chatbotConversation.appendChild(check)
                }

                chatbotConversation.scrollTop = chatbotConversation.scrollHeight
                context.push("assistant:" + data['content'])
                return;
            })
        }
    )
    .catch(err => {
        return console.log('Error :-S', err)
    });
})

document.querySelectorAll('.confirm-check').forEach((el) => {
    el.addEventListener('click', () => {
        let result = el.dataset.check
        console.log(result)
    });
});