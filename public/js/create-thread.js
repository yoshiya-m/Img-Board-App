document.addEventListener('DOMContentLoaded', function () {

    const createBtn = document.getElementById("create-thread-btn");
    const imgPreview = document.getElementById("img-preview");
    imgPreview.style.display ="none";
    const dropArea = document.getElementById('drop-area');
    const fileName = document.getElementById('file-name');
    
    let droppedFile;
    modalTitle = document.getElementById("modal-title"),
        modalMessage = document.getElementById("modal-message"),
        shareUrl = document.getElementById("share-url")
    deleteUrl = document.getElementById("delete-url")


    dropArea.addEventListener('dragover', (event) => {
        event.preventDefault(); // デフォルトの動作を防ぐ
        dropArea.classList.add('hover'); // スタイルの変更
    });

    // ドラッグリーブ時のイベント処理
    dropArea.addEventListener('dragleave', () => {
        dropArea.classList.remove('hover');
    });


    dropArea.addEventListener('drop', (event) => {
        event.preventDefault();
        dropArea.classList.remove('hover');

        droppedFile = event.dataTransfer.files[0]; // ドロップされたファイルを取得

        const reader = new FileReader();

        reader.onload = (e) => {
            imgPreview.src = e.target.result; // 読み込んだデータを img 要素の src に設定
            imgPreview.style.display = 'block'; // img 要素を表示
        };
        reader.readAsDataURL(droppedFile); // ファイルを読み込む


        console.log(droppedFile);
        fileName.innerHTML = droppedFile.name;
        console.log('file dropped');
    })


    // 投稿ボタンクリック
    createBtn.addEventListener('click', () => {
        console.log('clicked create thread');

        const subject = document.getElementById("subject").value;
        const content = document.getElementById("content").value;
        const formData = new FormData();
        formData.append('image', droppedFile);
        formData.append('subject', subject);
        formData.append('content', content);
        const currentPath = window.location.pathname; 
        if (currentPath === "/reply-form") {
            postId = document.getElementById("main-thread").getAttribute("data-post-id");
            formData.append("post_id", postId);
        }

        for (const [key, value] of formData.entries()) {
            console.log(`${key}:`, value);
        }
        url = 'https://img-board-app.yoshm.com/create';
        fetch(url, {
            method: 'POST',
            body: formData
        })
            .then(response => response.json())
            .then(data => {
                if (data.status === "success") {
                    if (currentPath === "/reply-form") {
                        window.location.href = 'https://img-board-app.yoshm.com/thread?post_id=' + postId;
                    } else {
                        window.location.href = 'https://img-board-app.yoshm.com/thread?post_id=' + data.post_id;
                    }
                    
                } else {
                    alert(data.message);
                }

            })
            .catch(error => {
                alert("エラー: " + error);
            })

    })



})