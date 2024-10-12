<div class="d-flex align-items-center m-3">
    <div class="drop-area d-flex justify-content-center align-items-center p-0 w-50" id="drop-area">
        <p>ここにファイルをドラッグ＆ドロップ</p>
    </div>
    <!-- 画像プレビュー -->
    <div class="w-50">
        <h6 class="text-center">画像プレビュー：<span id="file-name"></span></h6>
        <img id="img-preview" src="" class="img-fluid" alt="...">
    </div>
</div>
<div class="m-3">
    <div class="mb-3">
        <label for="subject" class="form-label">題名</label>
        <input type="text" class="form-control" id="subject">
    </div>
    <div class="mb-3">
        <label for="content" class="form-label">コメント</label>
        <textarea class="form-control" id="content" rows="3"></textarea>
    </div>
</div>

<div class="d-flex flex-column align-items-center justify-content-center m-3">
    <!-- 選択されたファイルを表示 -->
    <button id="create-thread-btn" type="button" class="btn btn-info m-2" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#staticBackdrop">投稿する</button>

    <!-- Modal -->
    <div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false"
        tabindex="-1" aria-labelledby="result-label" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header d-flex justify-content-center">
                    <h1 class="modal-title fs-5" id="modal-title"></h1>
                </div>
                <div class="modal-body d-flex flex-column justify-content-center align-items-center" id="modal-body">
                    <div>
                        <span id="modal-message"></span>
                    </div>
                    <div>
                        <span id="share-url"></span>
                    </div>
                    <div>
                        <span id="delete-url"></span>
                    </div>
                </div>
                <div class="modal-footer d-flex justify-content-center">
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
</div>

</div>

<script src="/js/create-thread.js"></script>