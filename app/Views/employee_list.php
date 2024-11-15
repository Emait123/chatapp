<?= $this->extend('layout/main.php') ?>

<?= $this->section('sidebar') ?>
    <?= $this->include('layout/sidebar.php', $user) ?>
<?= $this->endSection('sidebar') ?>

<?= $this->section('content') ?>
<main class="container-fluid d-flex flex-column justify-content-start align-items-start p-3" style="height: 100vh;">
    <div class="row">
        <div class="col-12">
            <h1>Danh sách nhân viên</h1>
            
            <!-- Button trigger modal -->
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModal">
                Thêm nhân viên
            </button>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <table class="table w-100">
                <tr>
                    <th>#</th>
                    <th>Tên đăng nhập</th>
                    <th>Tên nhân viên</th>
                    <th>ID Telegram</th>
                    <th>Tác vụ</th>
                </tr>
                <?php foreach($employeeList as $k => $employee): ?>
                    <tr>
                        <td><?= $k+1 ?></td>
                        <td><?= $employee['username'] ?></td>
                        <td><?= $employee['name'] ?></td>
                        <td><?= isset($employee['telegram_id']) ? $employee['telegram_id'] : '' ?></td>
                        <td>
                            <button type="button" class="btn btn-danger btn-delete" title="Xóa" data-employeeid="<?= $employee['employee_id'] ?>" data-userid="<?= $employee['user_id'] ?>" ><i class="fa-solid fa-trash"></i></button>
                            <button type="button" class="btn btn-info btn-edit" title="Sửa" data-userid="<?= $employee['user_id'] ?>" data-employeeid="<?= $employee['employee_id'] ?>" data-bs-toggle="modal" data-bs-target="#exampleModal"><i class="fa-solid fa-wrench"></i></button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </table>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="exampleModalLabel">Thêm nhân viên</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="" method="post" id='form-employee'>
                        <input type="hidden" name="user-id" value="" id="user-id">
                        <input type="hidden" name="employee-id" value="" id="employee-id">

                        <label class="form-label mb-3">Tên đăng nhập
                            <input type="text" class="form-control" name="username" id="username">
                        </label>
                        <label class="form-label mb-3">Mật khẩu
                            <input type="password" class="form-control" name="password" id="password">
                        </label>
                        <label class="form-label mb-3">Tên nhân viên
                            <input type="text" class="form-control" name="name" id="displayname">
                        </label>
                        <label class="form-label mb-3">ID Telegram
                            <input type="text" class="form-control" name="telegram_id" id="telegram-id">
                        </label>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                    <button type="submit" class="btn btn-primary" name="action" value="save" form="form-employee" id="btn-save">Thêm</button>
                    <button type="submit" class="btn btn-info" name="action" value="edit" form="form-employee" id="btn-edit" style="display: none;">Sửa đổi</button>
                </div>
            </div>
        </div>
    </div>
</main>
<script>
    document.querySelectorAll('.btn-delete').forEach( (el) => {
        el.addEventListener('click', () => {
            if (!confirm('Xóa nhân viên này?')) {
                return;
            }
            let id = el.dataset.id
            let formData = new FormData();
            formData.append('action', 'delete');
            formData.append('id', id);

            //Gửi request đến server để lấy thông tin
            fetch('<?= url_to('Employee::process') ?>', {
                method: 'POST',
                mode: 'no-cors',
                headers: { 'Content-Type': 'application/json' },
                body: formData
            })
            .then(
            (response) => {
                if (response.status !== 200) {
                    alert('Có lỗi khi xóa yêu cầu.');
                    return console.log('Lỗi, mã lỗi ' + response.status);
                }
                // parse response data
                response.json().then(data => {
                    console.log(data)
                    if (data['result'] == true) {
                        location.reload();
                    } else {
                        alert('Có lỗi khi xóa yêu cầu.');
                    }
                    return;
                })
            }
            )
            .catch(err => {
                alert('Có lỗi khi xóa yêu cầu.');
            return console.log('Error :-S', err)
            });
        });
    });

    document.querySelectorAll('.btn-edit').forEach( (el) => {
        el.addEventListener('click', () => {
            let userID = el.dataset.userid;
            let employeeID = el.dataset.employeeid;

            document.getElementById('user-id').value = userID;
            document.getElementById('employee-id').value = employeeID;

            let formData = new FormData();
            formData.append('action', 'getInfo');
            formData.append('userID', userID);
            formData.append('employeeID', employeeID);

            //Gửi request đến server để lấy thông tin
            fetch('<?= url_to('Employee::process') ?>', {
                method: 'POST',
                mode: 'no-cors',
                headers: { 'Content-Type': 'application/json' },
                body: formData
            })
            .then(
            (response) => {
                if (response.status !== 200) {
                    alert('Có lỗi khi xóa yêu cầu.');
                    return console.log('Lỗi, mã lỗi ' + response.status);
                }
                // parse response data
                response.json().then(data => {
                    // console.log(data)
                    if (data['result'] == true) {
                        let info = data['info'];
                        document.getElementById('username').value = info.username;
                        document.getElementById('displayname').value = info.name;
                        document.getElementById('telegram-id').value = info.telegram_id;
                        document.getElementById('btn-edit').style.display = 'block';
                        document.getElementById('btn-save').style.display = 'none';
                    } else {
                        alert('Có lỗi khi xóa yêu cầu.');
                    }
                    return;
                })
            }
            )
            .catch(err => {
                alert('Có lỗi khi xóa yêu cầu.');
            return console.log('Error :-S', err)
            });
        });
    });

    document.getElementById('exampleModal').addEventListener('hidden.bs.modal', () => {
        document.getElementById('form-employee').reset();
        document.getElementById('btn-edit').style.display = 'none';
        document.getElementById('btn-save').style.display = 'block';
    });
</script>
<?= $this->endSection('content') ?>