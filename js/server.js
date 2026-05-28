var ws = {
    server: null,
    taskId: null,
    canReload: true,

    init: function () {

        $('.c-task-current').click(function (e) {
            ws.taskId = $(this).data('taskId');
            e.stopPropagation();
        });

        $('.c-task-comment').click(function (e) {
            var link = $(this);
            e.stopPropagation();
            e.preventDefault();

            $.ajax({
                type: "post",
                dataType: "json",
                data: {
                    action: "taskPrepare",
                    taskId: $(this).data('taskId')
                },
                success: function (response) {

                    console.log('success', response)

                    if (response.success) {
                        $('#i-div-task-detail-' + link.data('taskId')).html(response.html).slideDown();
                    }
                },
                error: function (response) {
                    console.log('error', response)
                }
            });
        });

        //Запускаем проверку выполнения задания при возврате фокуса на страницу
        $('body').focusin(function () {

            if (ws.taskId != null) {
                var taskId = ws.taskId;
                ws.taskId = null;

                $('#i-div-task-' + taskId).slideUp('slow', function () {

                    $.ajax({
                        type: "post",
                        dataType: "json",
                        data: {
                            action: "taskCheck",
                            taskId: taskId
                        },
                        beforeSend: function () {

                            $('#i-div-task-detail-' + taskId).hide();

                            Sw.toast.fire({icon: 'info', title: 'Проверяем..'})

                        },
                        success: function (response) {

                            Swal.close()

                            console.log('success', response)

                            if (response.success) {

                                if (response.bonus) {
                                    location.reload();
                                }

                                $('#i-div-task-' + taskId).remove();
                                $('#i_header_balance').html(response.balance);
                                $('.loadbar').css({width: response.karma + '%'});
                                $('.karma .index').html('Карма ' + response.karmaText + '%');
                                if (response.html) {
                                    $('#i-div-tasks-list').append(response.html);
                                }

                                if (response.message) {
                                    Sw.toast.fire({icon: 'success', title: response.message })
                                }

                            } else {

                                $('#i-div-task-' + taskId).slideDown();

                                if (response.errorText) {
                                    $('#i-div-task-detail-' + taskId).html(response.errorText).show();
                                    Sw.toast.fire({icon: 'error', title: response.errorText})
                                }

                                if (response.removeTask !== undefined) {
                                    $('#i-div-task-' + taskId).remove()
                                    if (response.html) {
                                        $('#i-div-tasks-list').append(response.html)
                                    }
                                }

                            }
                            taskId = null;
                        },
                        error: function (response) {

                            console.log('error', response)

                            $('#i-div-task-' + taskId).slideDown().addClass('alert alert-danger');
                            taskId = null;
                        },
                        complete: function () {
                            ws.canReload = true;
                        }
                    });
                });
            }
        });
    }
};

$(document).ready(function () {
    ws.init();
});