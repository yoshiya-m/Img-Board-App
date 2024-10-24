
function setFilename(filename) {
    const filenameEle = document.getElementById('file-name');
    filenameEle.innerHTML = filename;
}

function initializeDropArea() {
    const dropArea = document.getElementById('drop-area');
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
        const fileInput = document.getElementById("file-input");

        const droppedFile = event.dataTransfer.files[0];
        const dataTransfer = new DataTransfer();
        dataTransfer.items.add(droppedFile);
        fileInput.files = dataTransfer.files;

        setFilename(droppedFile.name);
        setPreview(droppedFile);
    })
}



function initializeFileInput() {
    const fileBtn = this.document.getElementById("file-button");
    const fileInput = this.document.getElementById("file-input");
    fileBtn.addEventListener("click", () => {
        fileInput.click();
    });

    fileInput.addEventListener('change', (event) => {
        const file = event.target.files[0];
        if (file) {
            setPreview(file);
            setFilename(file.name);
        }
    });
}

function setPreview(imageFile) {
    const reader = new FileReader();
    const imgPreview = document.getElementById("img-preview");
    reader.onload = (e) => {
        imgPreview.src = e.target.result;
        imgPreview.style.display = 'block'; 
    };
    reader.readAsDataURL(imageFile); 
}

function isFilesizeValid(formData) {
    let totalSize = 0;

    for (const [key, value] of formData.entries()) {
        if (value instanceof File) {
            totalSize += value.size; 
        } else {
            totalSize += new Blob([`${key}=${value}`]).size; 
        }
    }
    if (totalSize >= 1000000) {
        const diff = totalSize - 1000000;
        alert('画像サイズが大きすぎます!\n' + diff + "バイト減らしてください");
        return false;
    }

    return true;
}

function getFormData() {
    const subject = document.getElementById("subject").value;
    const content = document.getElementById("content").value;
    const currentPath = window.location.pathname;
    const formData = new FormData();
    const fileInput = document.getElementById("file-input");
    file = fileInput.files[0];

    formData.append('image', file);
    formData.append('subject', subject);
    formData.append('content', content);
    if (currentPath === "/reply-form") {
        postId = document.getElementById("main-thread").getAttribute("data-post-id");
        formData.append("post_id", postId);
    }

    return formData;
}

function initializePreview() {
    const imgPreview = document.getElementById("img-preview");
    imgPreview.style.display = "none";
}

function initializeCreateBtn() {
    const createBtn = document.getElementById("create-thread-btn");
    const currentPath = window.location.pathname;
    createBtn.addEventListener('click', () => {

        const formData = getFormData();
        if (!isFilesizeValid(formData)) return;

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

}



document.addEventListener('DOMContentLoaded', function () {
    initializeFileInput();
    initializeDropArea();
    initializePreview();
    initializeCreateBtn();


})