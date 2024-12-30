function fetch_data(page = 1, column_name = 'reg_date', order = 'DESC', query = '') {
    $.ajax({
        url: "./home/ajax/notice_list_ajax.php",
        method: "POST",
        data: { page: page, column_name: column_name, order: order, query: query },
        success: function(data) {
            $('#notice_table').html(data);
        }
    });
}
$(document).ready(function() {
    fetch_data();
});
$('#search_input').on('keypress', function (e) {
    if (e.which == 13) {
        let query = $('#search_input').val();
        fetch_data(1, 'reg_date', 'DESC', query);
    }
});
$('#search_button').on('click', function () {
    let query = $('#search_input').val();
    fetch_data(1, 'reg_date', 'DESC', query);
});


function deleteNotice(id) {
    if (confirm("공지사항을 삭제하시겠습니까?")) {
        $.post('./home/request/delete_req.php', { code: 'notice_list', ids: [id] }, function (response) {
            alert(response);
            location.href = '../index.php?url=notice_list';
        }).fail(function () {
            alert('삭제 중 오류가 발생했습니다.');
        });
    }
}


document.querySelectorAll('.delete-file').forEach(function(button) {

    button.addEventListener('click', function() {

        var fileId = this.getAttribute('data-file-id');
        if (confirm('이 파일을 삭제하시겠습니까?')) {

            var xhr = new XMLHttpRequest();
            xhr.open('POST', './home/request/delete_req.php', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.onload = function() {
                if (xhr.status === 200) {
                    alert('파일이 삭제되었습니다.');
                    location.reload();
                } else {
                    alert('파일 삭제 중 오류가 발생했습니다.');
                }
            };
            xhr.send('code=delete_file&file_id=' + fileId);
        }

    });
});


document.getElementById('notice_form').addEventListener('submit', function (event) {

    event.preventDefault();
    const formData = new FormData(this);

    fetch('./home/request/notice_req.php', {
        method: 'POST',
        body: formData,
    })
        .then(response => response.text())
        .then(data => {
            alert(data);
            const modalElement = document.getElementById('addNoticeModal');
            const modalInstance = bootstrap.Modal.getInstance(modalElement);
            modalInstance.hide();
            location.reload();
        })
        .catch(error => {
            console.error('Error:', error);
        });

});