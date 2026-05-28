var special = {
    type: 'likes',
    prices: {},
    percents: {},
    percentsVals: {},
    userCityId: null,
    init: function () {
        $('.form-control').click(function(){
            special.calculateSum()
        }).change(function(){
            special.calculateSum()
        }).keyup(function(){
            special.calculateSum();
        });

        special.calculateSum();
    },
    calculateSum: function()
    {
        var balance = parseFloat($('#i_header_balance').html().replace(' ', ''));

        var type = $("input[name='type']:checked").val();
        var price = 0.0;
        var sum = 0.0;
        var i_sex = $('#i_sex').val();
        var ageFrom = $('select[name="ageFrom"]').val();
        var ageTo = $('select[name="ageTo"]').val();
        var city = $('select[name="city"] > option:selected');
        var i_relation = $('#i_relation').val();
        var i_avatarCount = $('#i_avatarCount').val();
        var i_filled = $('#i_filled').val();
        var i_pageAge = $('#i_pageAge').val();
        var i_followersCount = $('#i_followersCount').val();
        var i_interestingPage = $('#i_interestingPage').val();
        var i_frequencyPost = $('#i_frequencyPost').val();
        var i_check_prior = $('#i_check_prior').prop('checked');
        var i_task_minKarma = $('#i_task_minKarma > option:selected').val();

        for (i in special.prices)
        {
            if (i === type)
            {
                price = special.prices[i];
            }
        }

        price *= $('#i_task_count').val();
        sum = price;

        if (i_check_prior)
        {
            if (special.percents.percent_prior > 0)
            {
                sum += (price * special.percents.percent_prior/100);
            }
        }

        if (i_sex > 0)
        {
            if (special.percentsVals.sex[i_sex] > 0)
            {
                sum += (price * special.percentsVals.sex[i_sex]/100);
            }
            else
            {
                sum += (price * special.percents.percent_sex/100);
            }

        }
        if (ageFrom > 0)
        {
            if (special.percentsVals.ageFrom[ageFrom] > 0)
            {
                sum += (price * special.percentsVals.ageFrom[ageFrom]/100);
            }
            else {
                sum += (price * special.percents.percent_ageFrom / 100);
            }
        }
        if (ageTo > 0)
        {
            if (special.percentsVals.ageTo[ageTo] > 0)
            {
                sum += (price * special.percentsVals.ageTo[ageTo]/100);
            }
            else {
                sum += (price * special.percents.percent_ageTo / 100);
            }
        }
        if (city.val() > 0)
        {
            if (city.data('type') == 'country')
                sum += (price * special.percents.percent_country/100);
            else
            {
                if (parseInt(city.val()) === special.userCityId)
                {
                    sum += (price * special.percents.percent_city_my/100);
                }
                else
                {
                    sum += (price * special.percents.percent_city/100);
                }
            }
        }
        if (i_relation > 0)
        {
            if (special.percentsVals.relation[i_relation] > 0)
            {
                sum += (price * special.percentsVals.relation[i_relation]/100);
            }
            else {
                sum += (price * special.percents.percent_relation / 100);
            }
        }
        if (i_avatarCount > 0)
        {
            if (special.percentsVals.avatarCount[i_avatarCount] > 0)
            {
                sum += (price * special.percentsVals.avatarCount[i_avatarCount]/100);
            }
            else {
                sum += (price * special.percents.percent_avatarCount / 100);
            }
        }
        if (i_filled > 0)
        {
            if (special.percentsVals.filled[i_filled] > 0)
            {
                sum += (price * special.percentsVals.filled[i_filled]/100);
            }
            else {
                sum += (price * special.percents.percent_filled / 100);
            }
        }
        if (i_pageAge > 0)
        {
            if (special.percentsVals.pageAge[i_pageAge] > 0)
            {
                sum += (price * special.percentsVals.pageAge[i_pageAge]/100);
            }
            else {
                sum += (price * special.percents.percent_pageAge / 100);
            }
        }
        if (i_followersCount > 0)
        {
            if (special.percentsVals.followersCount[i_followersCount] > 0)
            {
                sum += (price * special.percentsVals.followersCount[i_followersCount]/100);
            }
            else {
                sum += (price * special.percents.percent_followersCount / 100);
            }
        }
        if (i_interestingPage > 0)
        {
            if (special.percentsVals.interestingPage[i_interestingPage] > 0)
            {
                sum += (price * special.percentsVals.interestingPage[i_interestingPage]/100);
            }
            else {
                sum += (price * special.percents.percent_interestingPage / 100);
            }
        }
        if (i_frequencyPost > 0)
        {
            if (special.percentsVals.frequencyPost[i_frequencyPost] > 0)
            {
                sum += (price * special.percentsVals.frequencyPost[i_frequencyPost]/100);
            }
            else {
                sum += (price * special.percents.percent_frequencyPost / 100);
            }
        }
        if (i_task_minKarma)
        {
            if (special.percentsVals.minKarma[i_task_minKarma] > 0)
            {
                sum += (price * special.percentsVals.minKarma[i_task_minKarma]/100);
            }
        }

        $('#i_task_sum').val(sum + ' баллов');
        if (sum > balance)
        {
            $('#i_div_sum').addClass('has-error').removeClass('has-success');
            $('#i_div_sum_result').show();
            $('#i_button_submit').attr('disabled', true);
        }
        else
        {
            $('#i_button_submit').attr('disabled', false);
            $('#i_div_sum_result').hide();
            $('#i_div_sum').addClass('has-success').removeClass('has-error');
        }
    }
}
$(document).ready(function(){
    special.init();
});