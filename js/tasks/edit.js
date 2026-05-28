var task_edit = {
    type: 'likes',
    prices: {},
    percents: {},
    percentsVals: {},
    userCityId: null,
    members_count: 0,
    limits: {},
    accept: false,
    init: function () {

        $('.form-control').click(function () {
            task_edit.calculateSum()
        }).change(function () {
            task_edit.calculateSum()
        }).keyup(function () {
            task_edit.calculateSum()
        });
        $('.btn').click(function () {
            task_edit.calculateSum();
        });
        $(":checkbox").click(function () {
            task_edit.calculateSum();
        }).change(function () {
            task_edit.calculateSum();
        });

        $('select.form-control').click(function () {
            if ($(this).val() > 0) {
                $(this).addClass('alert-info');
            } else {
                $(this).removeClass('alert-info');
            }
        });

        $("select[name='vkType']").change(function () {
            $('#i_task_url').focus();
            $('#i_task_url_last').val('');
            $('#i_div_task_url_result_text').html('');
            let val = $('#i_task_url').val();
            if (val) {
                task_edit.chekUrl();
            }
        });

        $("input[name='type']").change(function () {
            let val = $("input[name='type']:checked").val();
            task_edit.type = val;
            $('.c-tip-type').hide();
            $('.c-tip-vkType').hide();

            $('#i-tip-type-' + val).show();

            $('#i_task_url').val('').focus();
            $('#i_task_url_last').val('');
            $('#i_div_task_url_result_text').html('');


            $('#i_div_comments').hide();
            $('#i_div_targeting_container').show();
            $('#i_div_vkTypes').hide();
            $('#i_vkType_label_comment').show();
            switch (val) {
                case "likes":
                    $('#i_div_vkTypes').show();
                    $('#i_vkType_comment').show();
                    task_edit.vkTypeChange();
                case "reposts":
                    $('#i_div_vkTypes').show();
                    $('#i_vkType_comment').show();
                    task_edit.vkTypeChange();
                    $('#i_div_minKarma').show();
                    break;
                case "views":
                case "video":
                    $('#i_div_minKarma').hide();
                    $('#i_div_targeting_container').hide();
                    break;
                case 'comments':
                    $('#i_vkType_comment').hide();
                    $('#i_div_vkTypes').show();
                    task_edit.vkTypeChange();
                    $('#i_div_comments').show();
                case 'polls':
                    $('#i_task_url_last').val('');
                    if ($('#i_task_url').val().length > 0) {
                        task_edit.chekUrl();
                    }
            }
        });

        $('#i_task_url').on('paste', function () {
            let element = this;
            setTimeout(function () {
                let text = $(element).val();
                task_edit.chekUrl();
            }, 100);
        });

        $('#i_task_url').change(function () {
            task_edit.chekUrl();
        });

        $('#i_commentType').click(function () {
            val = $(this).val();
            if (val == 3) {
                $('#i_div_comments_list').show();
            } else {
                $('#i_div_comments_list').hide();
            }
        });
        $('.c_input_comments').keyup(function (e) {
            task_edit.commentEdit($(this), e);
        });

        task_edit.calculateSum();
    },
    chekUrl: function () {
        let type = $("input[name='type']:checked").val();
        let vkType = $("select[name='vkType'] option:selected").val();
        if (type == 'likes' || type == 'reposts') {
            if (!vkType) {
                alert('Укажите расположение');
            }
        }
        if (type === "polls") {
            let val = $('#i_task_url').val();
            if (val === '') {
                let div = $('#i_div_task_url_result_text');
                div.html('<div class="alert alert-danger">Укажите ссылку</div>');
            } else {
                if ($('#i_task_url_last').val() == val)
                    return;

                $('#i_task_url_last').val(val);
                $.ajax({
                    type: "post",
                    dataType: "json",
                    data: {
                        action: "get_poll",
                        url: $('#i_task_url').val()
                    },
                    beforeSend: function () {
                        let div = $('#i_div_task_url_result_text');
                        div.html('<div class="progress"><div class="progress-bar progress-bar-striped active" role="progressbar" style="width: 100%"></div></div>');
                    },
                    success: function (data) {
                        if (data.success) {
                            let html = '';
                            let div = $('#i_div_task_url_result_text');
                            html += '<h4>' + data.poll.poll.question + '</h4>';
                            html += '<input type="hidden" name="isAnonymous" value="' + data.poll.poll.anonymous + '" />';
                            html += '<div><label><input type="radio" name="answerId" value="0" checked="checked" />Любой вариант</label></div>';
                            let answerIds = [];
                            if (!data.poll.poll.anonymous) {
                                for (i in data.poll.answers) {
                                    answerIds.push(data.poll.answers[i].id);
                                    html += '<div><label><input type="radio" name="answerId" value="' + data.poll.answers[i].id + '" /> ' + data.poll.answers[i].title + '</label></div>';
                                    html += '<input type="hidden" name="answers[' + data.poll.answers[i].id + ']" value="' + data.poll.answers[i].title + '" />';
                                }
                            }

                            html += '<input type="hidden" name="answerIds" value="' + answerIds.join(',') + '" />';
                            html += '<input type="hidden" name="pollId" value="' + data.poll.poll.id + '" />';
                            div.html(html);
                        } else {
                            let div = $('#i_div_task_url_result_text');
                            div.html('<div class="alert alert-danger">' + data.errorText + '</div>');
                        }
                    },
                    error: function () {
                        let div = $('#i_div_task_url_result_text');
                        div.html('<div class="alert alert-danger">Не удалось выполнить запрос. Обратитесь в техподдержку!</div>');
                    }
                });
            }
        } else {

            let val = $('#i_task_url').val();
            if (val === '') {
                let div = $('#i_div_task_url_result_text');
                div.html('<div class="alert alert-danger">Укажите ссылку</div>');
            } else {
                if ($('#i_task_url_last').val() == val)
                    return;

                $('#i_task_url_last').val(val);

                $.ajax({
                    type: "post",
                    dataType: "json",
                    data: {
                        action: "checkUrl",
                        url: $('#i_task_url').val(),
                        type: type,
                        vkType: vkType
                    },
                    beforeSend: function () {
                        let div = $('#i_div_task_url_result_text');
                        div.html('<div class="progress"><div class="progress-bar progress-bar-striped active" role="progressbar" style="width: 100%"></div></div>');
                    },
                    success: function (data) {

                        if (data.success) {
                            let div = $('#i_div_task_url_result_text');
                            div.html('<div class="alert alert-success">Ссылка подходит для данного задания</div>');
                            if (data.age_limits > 2) {
                                div.html('<div class="alert alert-success">Ссылка подходит для данного задания</div><div class="alert alert-warning">В Вашем сообществе обнаружен 18+ контент. Скорость выполнения заданий для этого сообщества может быть снижена, в связи с отсутствием желания у пользователей посещать сообщества 18+</div>');
                            }

                            task_edit.members_count = data.members_count;
                            task_edit.limits = data.limits;
                        } else {
                            let div = $('#i_div_task_url_result_text');
                            div.html('<div class="alert alert-danger">' + data.errorText + '</div>');
                        }
                    },
                    error: function () {
                        let div = $('#i_div_task_url_result_text');
                        div.html('<div class="alert alert-danger">Не удалось выполнить запрос. Обратитесь в техподдержку!</div>');
                    }
                });
            }
        }
    },
    commentEdit: function (comment, e) {

        if (e.keyCode == 9)
            return false;

        if (e.keyCode == 13) {
            e.stopPropagation();
            e.preventDefault();
            return false;
        }

        $('.c_input_comments').each(function () {
            let val = $(this).val();
            if (val.length == 0) {
                $(this).remove();
            }
        });

        if ($('.c_input_comments').length < 25) {
            let commentInput = $('<input class="form-control c_input_comments" name="comments[]" placeholder="Введите сюда текст комментария" />');
            commentInput.keyup(function (e) {
                task_edit.commentEdit($(this), e);
            });
            $('#i_div_comments_list').append(commentInput);
        }
    },
    calculateSum: function () {
        let balance = parseFloat($('#i_header_balance').html().replace(' ', ''));

        let type = $("input[name='type']:checked").val();
        let price = 0.0;
        let sum = 0.0;
        let i_sex = $('#i_sex').val();
        let ageFrom = $('select[name="ageFrom"]').val();
        let ageTo = $('select[name="ageTo"]').val();
        let city = $('select[name="city"] > option:selected');
        let i_relation = $('#i_relation').val();
        let i_avatarCount = $('#i_avatarCount').val();
        let i_filled = $('#i_filled').val();
        let i_pageAge = $('#i_pageAge').val();
        let i_followersCount = $('#i_followersCount').val();
        let i_interestingPage = $('#i_interestingPage').val();
        let i_frequencyPost = $('#i_frequencyPost').val();
        let i_check_prior = $('#i_check_prior').prop('checked');
        let i_task_minKarma = $('#i_task_minKarma > option:selected').val();

        for (i in task_edit.prices) {
            if (i === type) {
                price = task_edit.prices[i];
            }
        }

        sum = price;

        if (i_check_prior) {
            if (task_edit.percents.percent_prior > 0) {
                sum += (price * task_edit.percents.percent_prior / 100);
            }
        }

        if (i_sex > 0) {
            if (task_edit.percentsVals.sex[i_sex] > 0) {
                sum += (price * task_edit.percentsVals.sex[i_sex] / 100);
            } else {
                sum += (price * task_edit.percents.percent_sex / 100);
            }

        }
        if (ageFrom > 0) {
            if (task_edit.percentsVals.ageFrom[ageFrom] > 0) {
                sum += (price * task_edit.percentsVals.ageFrom[ageFrom] / 100);
            } else {
                sum += (price * task_edit.percents.percent_ageFrom / 100);
            }
        }
        if (ageTo > 0) {
            if (task_edit.percentsVals.ageTo[ageTo] > 0) {
                sum += (price * task_edit.percentsVals.ageTo[ageTo] / 100);
            } else {
                sum += (price * task_edit.percents.percent_ageTo / 100);
            }
        }
        if (city.val() > 0) {
            if (city.data('type') == 'country')
                sum += (price * task_edit.percents.percent_country / 100);
            else {
                if (parseInt(city.val()) === task_edit.userCityId) {
                    sum += (price * task_edit.percents.percent_city_my / 100);
                } else {
                    sum += (price * task_edit.percents.percent_city / 100);
                }
            }
        }
        if (i_relation > 0) {
            if (task_edit.percentsVals.relation[i_relation] > 0) {
                sum += (price * task_edit.percentsVals.relation[i_relation] / 100);
            } else {
                sum += (price * task_edit.percents.percent_relation / 100);
            }
        }
        if (i_avatarCount > 0) {
            if (task_edit.percentsVals.avatarCount[i_avatarCount] > 0) {
                sum += (price * task_edit.percentsVals.avatarCount[i_avatarCount] / 100);
            } else {
                sum += (price * task_edit.percents.percent_avatarCount / 100);
            }
        }
        if (i_filled > 0) {
            if (task_edit.percentsVals.filled[i_filled] > 0) {
                sum += (price * task_edit.percentsVals.filled[i_filled] / 100);
            } else {
                sum += (price * task_edit.percents.percent_filled / 100);
            }
        }
        if (i_pageAge > 0) {
            if (task_edit.percentsVals.pageAge[i_pageAge] > 0) {
                sum += (price * task_edit.percentsVals.pageAge[i_pageAge] / 100);
            } else {
                sum += (price * task_edit.percents.percent_pageAge / 100);
            }
        }
        if (i_followersCount > 0) {
            if (task_edit.percentsVals.followersCount[i_followersCount] > 0) {
                sum += (price * task_edit.percentsVals.followersCount[i_followersCount] / 100);
            } else {
                sum += (price * task_edit.percents.percent_followersCount / 100);
            }
        }
        if (i_interestingPage > 0) {
            if (task_edit.percentsVals.interestingPage[i_interestingPage] > 0) {
                sum += (price * task_edit.percentsVals.interestingPage[i_interestingPage] / 100);
            } else {
                sum += (price * task_edit.percents.percent_interestingPage / 100);
            }
        }
        if (i_frequencyPost > 0) {
            if (task_edit.percentsVals.frequencyPost[i_frequencyPost] > 0) {
                sum += (price * task_edit.percentsVals.frequencyPost[i_frequencyPost] / 100);
            } else {
                sum += (price * task_edit.percents.percent_frequencyPost / 100);
            }
        }
        if (i_task_minKarma) {
            if (task_edit.percentsVals.minKarma[i_task_minKarma] > 0) {
                sum += (price * task_edit.percentsVals.minKarma[i_task_minKarma] / 100);
            }
        }

        $('#i_task_price').val(task_edit.number_format(sum, '2', '.', ' ') + ' баллов');
        sum *= $('#i_task_count').val();

        $('#i_task_sum').val(task_edit.number_format(sum, '2', '.', ' ') + ' баллов');
        if (sum > balance) {
            $('#i_div_sum').addClass('has-error').removeClass('has-success');
            $('#i_div_sum_result').show();
            $('#i_button_submit').attr('disabled', true);
        } else {
            $('#i_button_submit').attr('disabled', false);
            $('#i_div_sum_result').hide();
            $('#i_div_sum').addClass('has-success').removeClass('has-error');
        }
    },
    vkTypeChange: function () {
        let type = $("input[name='type']:checked").val();
        let vkType = $("select[name='vkType'] option:selected").val();

        $('.c-tip-vkType').hide();
        $('#i-tip-vkType-' + type + '-' + vkType).show();
    },
    number_format: function (number, decimals, dec_point, thousands_sep) {	// Format a number with grouped thousands
        //
        // +   original by: Jonas Raoni Soares Silva (http://www.jsfromhell.com)
        // +   improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
        // +	 bugfix by: Michael White (http://crestidg.com)

        let i, j, kw, kd, km;

        // input sanitation & defaults
        if (isNaN(decimals = Math.abs(decimals))) {
            decimals = 2;
        }
        if (dec_point == undefined) {
            dec_point = ",";
        }
        if (thousands_sep == undefined) {
            thousands_sep = ".";
        }

        i = parseInt(number = (+number || 0).toFixed(decimals)) + "";

        if ((j = i.length) > 3) {
            j = j % 3;
        } else {
            j = 0;
        }

        km = (j ? i.substr(0, j) + thousands_sep : "");
        kw = i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + thousands_sep);
        //kd = (decimals ? dec_point + Math.abs(number - i).toFixed(decimals).slice(2) : "");
        kd = (decimals ? dec_point + Math.abs(number - i).toFixed(decimals).replace(/-/, 0).slice(2) : "");


        return km + kw + kd;
    }
};

$(document).ready(function () {
    task_edit.init();
});