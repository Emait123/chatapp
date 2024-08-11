const chatbotConversation = document.getElementById('chatbot-conversation')
const context = []
const detail = {}
const chatMode = 'question'

function addContext(text, source) {
    if (context.length >= 10) {
        //Nếu context nhiều hơn 10 thì bỏ phần từ đầu tiên đi
        context.shift()
    }
    context.push(source + ":" + text)
}

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
                addContext(question, 'user')
                document.getElementById('pending').remove()
                console.log(data)

                if (data['type'] == 'chat') {
                    let answer = document.createElement('div')
                    answer.classList.add('speech', 'speech-ai')
                    answer.textContent = data['content']
                    chatbotConversation.appendChild(answer)
                }

                if (data['type'] == 'check') {
                    let answer = document.createElement('div')
                    answer.classList.add('speech', 'speech-ai')
                    answer.textContent = `Bạn muốn xác nhận nghỉ phép với các thông tin sau? Ngày: ${data['date']} ${data['time']}, lý do: ${data['reason']}`
                    chatbotConversation.appendChild(answer)

                    let check = document.createElement('div')
                    check.classList.add('speech', 'speech-ai', 'd-flex')
                    check.setAttribute('id', 'check')
                    check.innerHTML = "<button type='button' class='btn btn-success confirm-check' title='Đồng ý' data-check='yes' onclick=confirmTimeoff(event)><i class='fa-solid fa-check'></i></button><button type='button' class='btn btn-danger confirm-check' title='Không đồng ý' data-check='no' onclick=confirmTimeoff(event)><i class='fa-solid fa-x'></i></button>"
                    chatbotConversation.appendChild(check)
                }

                if (data['type'] == 'incomplete') {
                    let date = data['date'] == '' ? 'Chưa rõ' : data['date']
                    let time = data['time'] == '' ? '' : data['time']
                    let reason = data['reason'] == '' ? 'Chưa rõ' : data['reason']

                    let answer = document.createElement('div')
                    answer.classList.add('speech', 'speech-ai')
                    answer.textContent = `Thông tin nghỉ phép của bạn hiện giờ là: Ngày ${date} ${time}, lý do: ${reason}. Vui lòng bổ sung thông tin còn thiếu.`
                    chatbotConversation.appendChild(answer)
                }

                chatbotConversation.scrollTop = chatbotConversation.scrollHeight
                addContext(data['content'], 'assistant')
                return;
            })
        }
    )
    .catch(err => {
        return console.log('Error :-S', err)
    });
})

document.getElementById('clear-session').addEventListener('click', () => {
        //Gửi request đến server để lấy thông tin
        let formData = new FormData();
        formData.append('action', 'clear-session');
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
                    if (data['result'] == true) {
                        location.reload();
                    }
                    return;
                })
            }
        )
        .catch(err => {
            return console.log('Error :-S', err)
        });
});

function confirmTimeoff(event) {
    let confirm = event.target.dataset.check
    document.getElementById('check').remove()
    if (confirm == 'no') {
        let answer = document.createElement('div')
        answer.classList.add('speech', 'speech-ai')
        let text = 'Bạn muốn thay đổi thông tin nào?'
        addContext(text, 'assistant')
        answer.textContent = text
        chatbotConversation.appendChild(answer)
        chatbotConversation.scrollTop = chatbotConversation.scrollHeight
        return
    }

    //Gửi request đến server để lấy thông tin
    let formData = new FormData();
    formData.append('action', 'confirm-timeoff');
    formData.append('confirm', confirm);
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
                console.log(data)
                if (data['result'] == true) {
                    let answer = document.createElement('div')
                    answer.classList.add('speech', 'speech-ai')
                    let text = 'Thông tin nghỉ phép của bạn đã được ghi nhận.'
                    answer.textContent = text
                    chatbotConversation.appendChild(answer)
                    chatbotConversation.scrollTop = chatbotConversation.scrollHeight
                }
                return;
            })
        }
    )
    .catch(err => {
        return console.log('Error :-S', err)
    });
}