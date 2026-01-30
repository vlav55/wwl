    <!-- Button trigger modal for ma_clients.php -->
    <button type="button" class="btn btn-link" data-toggle="modal" data-target="#myModal">
        Контрагенты
    </button>

    <!-- Modal for ma_clients.php -->
    <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="myModalLabel">ma_clients.php</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body pt-0 mt-0">
                    <iframe src="ma_clients.php" style="width: 100%; height: 400px; border: none;"></iframe>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Button trigger modal for ma_cat.php -->
    <button type="button" class="btn btn-link" data-toggle="modal" data-target="#catModal">
        Категории
    </button>

    <!-- Modal for ma_cat.php -->
    <div class="modal fade" id="catModal" tabindex="-1" role="dialog" aria-labelledby="catModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="catModalLabel">ma_cat.php</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body pt-0 mt-0">
                    <iframe src="ma_cat.php" style="width: 100%; height: 400px; border: none;"></iframe>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Button trigger modal for ma_acc.php -->
    <button type="button" class="btn btn-link" data-toggle="modal" data-target="#accModal">
        Счета
    </button>

    <!-- Modal for ma_acc.php -->
    <div class="modal fade" id="accModal" tabindex="-1" role="dialog" aria-labelledby="accModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="accModalLabel">ma_acc.php</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body pt-0 mt-0">
                    <iframe src="ma_acc.php" style="width: 100%; height: 400px; border: none;"></iframe>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

<!-- Add New Entry Button -->
<div class="row">
    <div class="col text-left my-3">
        <button type="button" class="btn btn-primary btn-lg" data-toggle="modal" data-target="#ma_op_addModal">
            Добавить
        </button>
    </div>
</div>

<!-- Modal for adding new entry -->
<div class="modal fade" id="ma_op_addModal" tabindex="-1" role="dialog" aria-labelledby="ma_op_addModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="ma_op_addModalLabel">Добавить/редактировать</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body py-0">
                <iframe id="modalFrame" src="ma_op_add.php" style="width: 100%; height: 550px; border: none;"></iframe>
            </div>
        </div>
    </div>
</div>
