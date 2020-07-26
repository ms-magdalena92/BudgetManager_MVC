function signForm()
{
    validateSignForm();
    showHidePassword()
}

function validateSignForm()
{
    $.validator.addMethod('validName',
        function(value, element, param) {
            
            if (value != '') {
                
                if (value.match(/^[A-Za-z]+$/)) {
                    
                    return true;
                }
               }
            return false;
        },
        
        'Name must contain letters only, special characters not allowed.'
    );

    $.validator.addMethod('validPassword',
        function(value, element, param) {
            
            if (value != '') {
                
                if (value.match(/.*[a-z]+.*/i) == null) {
                    
                    return false;
                }
                
                if (value.match(/.*\d+.*/) == null) {
                    
                    return false;
                }
            }
            return true;
        },
        
        'Password must contain at least one letter and at least one number.'
    );
            
    $(document).ready(function() {
        $('#signupForm').validate({
            errorElement: 'li',
            rules: {
                userName: {
                    required: true,
                    minlength: 2,
                    maxlength: 20,
                    validName: true
                },
                email: {
                    required: true,
                    email: true,
                    remote: '/account/validate-email'
                },
                password: {
                    required: true,
                    minlength: 8,
                    maxlength: 50,
                    validPassword: true
                },
                passwordConfirm: {
                    required: true,
                    equalTo: '#password1'
                }
            },
            messages: {
                userName: {
                    required: 'Name is required.',
                    minlength: 'Name needs to be between 2 to 20 characters.',
                    maxlength: 'Name needs to be between 2 to 20 characters.'
                },
                email: {
                    required: 'E-mail adress is required.',
                    email: 'Please enter a valid e-mail adress.',
                    remote: 'An account with this e-mail adress already exists.'
                },
                password: {
                    required: 'Password is required.',
                    minlength: 'Password needs to be between 8 to 50 characters.',
                    maxlength: 'Password needs to be between 8 to 50 characters.'
                },
                passwordConfirm: {
                    required: 'Password confirmation is required.',
                    equalTo: 'Passwords you have entered does not match.'
                }
            },
            errorPlacement: function(error,element){
                
                if(element.attr('name') == 'userName') {
                    error.appendTo('#nameError');
                }
                if(element.attr('name') == 'email') {
                    error.appendTo('#emailError');
                }
                if(element.attr('name') == 'password') {
                    error.appendTo('#passwordError');
                }
                if(element.attr('name') == 'passwordConfirm') {
                    error.appendTo('#passwordConfirmError');
                }
            }	
        });
    });
}

function validateItemsForm()
{
    $.validator.addMethod('validComment',
        function(value, element, param) {
            
            if (value != '') {
                
                if (value.match(/^[A-ZĄĘÓŁŚŻŹĆŃa-ząęółśżźćń 0-9]+$/)) {
                    
                    return true;
                }
                return false;
            }
            return true;
        },
        
        'Comment can contain up to 100 characters - only letters and numbers allowed.'
    );


    $(document).ready(function() {
        $('#itemsForm').validate({
            errorElement: 'li',
            rules: {
                amount: {
                    required: true,
                    number: true,
                    min: 0,
                    max: 999999.99,
                    step: 0.01
                },
                date: {
                    required: true,
                    date: true
                },
                category: {
                    required: true
                },
                payment: {
                    required: true
                },
                comment: {
                    validComment: true,
                    maxlength: 100
                }
            },
            messages: {
                amount: {
                    required: 'Amount is required.',
                    number: 'Enter valid positive amount - maximum 6 integer digits and 2 decimal places.',
                    min: 'Enter valid positive amount - maximum 6 integer digits and 2 decimal places.',
                    max: 'Enter valid positive amount - maximum 6 integer digits and 2 decimal places.'
                },
                date: {
                    required: 'Date is required.'
                },
                category: {
                    required: 'Category is required.'
                },
                payment: {
                    required: 'Payment method is required.'
                },
            },
            errorPlacement: function(error,element){
                
                if(element.attr('name') == 'amount') {
                    error.appendTo('#amountError');
                }
                if(element.attr('name') == 'date') {
                    error.appendTo('#dateError');
                }
                if(element.attr('name') == 'category') {
                    error.appendTo('#categoryError');
                }
                if(element.attr('name') == 'comment') {
                    error.appendTo('#commentError');
                }
                if(element.attr('name') == 'payment') {
                    error.appendTo('#paymentError');
                }
            }	
        });
    });
}

function showHidePassword()
{
    $(".reveal").on('click',function() {
        
        var $password = $("#password1");
        
        if ($password.attr('type') === 'password') {
            
            $password.attr('type', 'text');
            $("#toggler").removeClass("icon-eye");
            $("#toggler").addClass("icon-eye-off");
            
        } else {
            
            $password.attr('type', 'password');
            $("#toggler").removeClass("icon-eye-off");
            $("#toggler").addClass("icon-eye");
        }
    });
}

function drawChart(incomes, expenses)
{
    
    google.charts.load('current', {'packages':['corechart']});
    
    if(Array.isArray(incomes) && incomes.length) {
        
        google.charts.setOnLoadCallback(drawIncomesChart);
    }
                
    if(Array.isArray(expenses) && expenses.length) {
        
        google.charts.setOnLoadCallback(drawExpensesChart);
    }
    
    function drawIncomesChart() {
        
        var incomesData = new google.visualization.DataTable();
        incomesData.addColumn('string', 'Category');
        incomesData.addColumn('number', 'Amount');
        
		for (var i = 0; i < incomes.length; i++) {
            
            incomesData.addRow([incomes[i].income_category, parseFloat(incomes[i].income_amount)]);
        }
        
        var incomesOptions = {
            title: 'Income',
            colors: ['#ffad33', '#ffc266', '#ffd699', '#ff9900', '#e68a00'],
            backgroundColor: { fill:'transparent' },
            chartArea:{top:30,bottom:10,width:'100%',height:'100%'},
            fontSize: 17,
            fontName: 'Nunito',
            legend: {position: 'right', textStyle: {color: '#404040', fontSize: 17}},
            titleTextStyle: {color: '#404040', fontSize: 17},
            pieSliceTextStyle: {color: '#404040', fontSize: 17},
            tooltip: {textStyle: {color: '#404040', fontSize: 15}}
        };
        
        var incomesChart = new google.visualization.PieChart(document.getElementById('piechart1'));
        incomesChart.draw(incomesData, incomesOptions);
    }
    
    function drawExpensesChart() {
        
        var expensesData = new google.visualization.DataTable();
        expensesData.addColumn('string', 'Category');
        expensesData.addColumn('number', 'Amount');
        
        var expensesOptions = {
            title: 'Expenses',
            colors: ['#ffad33', '#ffc266', '#ffd699', '#ff9900', '#e68a00'],
            backgroundColor: { fill:'transparent' },
            chartArea:{top:30,bottom:10,width:'100%',height:'100%'},
            fontSize: 17,
            fontName: 'Nunito',
            legend: {position: 'right', textStyle: {color: '#404040', fontSize: 17}},
            titleTextStyle: {color: '#404040', fontSize: 17},
            pieSliceTextStyle: {color: '#404040', fontSize: 17},
            tooltip: {textStyle: {color: '#404040', fontSize: 15}}
        };
        
        for (var i = 0; i < expenses.length; i++) {
            
            expensesData.addRow([expenses[i].expense_category, parseFloat(expenses[i].expense_amount)]);
        }
        
        var expensesChart = new google.visualization.PieChart(document.getElementById('piechart2'));
        expensesChart.draw(expensesData, expensesOptions);
    }
}

function expandTableRows()
{
    $('tr.header').click(function(){
        
        $(this).nextUntil('tr.header').css('display', function(i,v) {
            
            return this.style.display === 'table-row' ? 'none' : 'table-row';
        });
    });
}

function validateCategoryForm()
{
    $.validator.setDefaults({
        ignore: ':hidden, [readonly=readonly]'
    });
    
    $.validator.addMethod('validName',
        function(value, element, param) {
            
            if (value != '') {
                
                if (value.match(/^[A-ZĄĘÓŁŚŻŹĆŃa-ząęółśżźćń 0-9]+$/)) {
                    
                    return true;
                }
                return false;
            }
            return true;
        },
        
        'Name can contain up to 50 characters - only letters and numbers allowed.'
    );
    
    $(document).ready(function() {

        $('form').each(function() {
            $(this).validate({
                errorElement: 'li',
                rules: {
                    categoryNewName: {
                        validName: true,
                        minlength: 2,
                        maxlength: 50,
                        remote: {
                            url: '/settings/validate-category',
                            type: 'post',
                            data: {
                                categoryType: function() {
                                    return $('input[name="categoryNewName"]').attr('data-category-type');
                                },
                                categoryOldId: function() {
                                    return $('input[name="categoryOldId"]').val();
                                }
                            }
                        }
                    },
                    limitAmount: {
                        required: true,
                        number: true,
                        min: 0.01,
                        max: 999999.99,
                        step: 0.01
                    }
                },
                messages: {
                    categoryNewName: {
                        required: 'Name is required.',
                        remote: 'Name already exists.'
                    },
                    limitAmount: {
                        required: 'Amount is required.',
                        number: 'Enter valid positive amount - maximum 6 integer digits and 2 decimal places.',
                        min: 'Enter valid positive amount - maximum 6 integer digits and 2 decimal places.',
                        max: 'Enter valid positive amount - maximum 6 integer digits and 2 decimal places.'
                    }
                },
                errorPlacement: function(error,element){
                    
                    if(element.attr('name') == 'categoryNewName') {
                        
                        error.appendTo('.categoryNameError');
                    }

                    if(element.attr('name') == 'limitAmount') {
                        
                        error.appendTo('.categoryLimitError');
                    }
                }
            });
        });
    });
}

function manageModal()
{
    $('.modal').on('hidden.bs.modal', function () {
        
        $('input[name="monthlyLimit"]').prop('checked', false);
        $('input[name="limitAmount"]').prop('readonly', true);
        $('.limitLabel').addClass('text-muted');
        $('input[name="limitAmount"]').val('');
        $('.error').empty();
    });
    
    $(document).on('click', '#deleteButton', function() {

        $('#deleteCategoryModal input[name="categoryOldId"]').val($(this).attr('data-category-id'));
    });

    $(document).on('click', '#addButton', function() {

        $('#addCategoryModal input[name="categoryNewName"]').val('');
    });
    
    $(document).on('click', '#editButton', function() {

        $('#editCategoryModal input[name="categoryNewName"]').val($(this).attr('data-category-name'));
        $('#editCategoryModal input[name="categoryOldId"]').val($(this).attr('data-category-id'));
        
        if($(this).attr('data-limit')) {
            $('input[name="monthlyLimit"]').prop('checked', true);
            $('input[name="limitAmount"]').prop('readonly', false);
            $('.limitLabel').removeClass('text-muted');
        }

        if($(this).attr('data-limit-amount') != 0) {
            
            $('input[name="limitAmount"]').val($(this).attr('data-limit-amount'));
        }

    });

    $('input[name="monthlyLimit"]').on('change', function() {

        if(this.checked){
            $('input[name="limitAmount"]').prop('readonly', false);
            $('.limitLabel').removeClass('text-muted');
        } else {
            $('input[name="limitAmount"]').prop('readonly', true);
            $('.limitLabel').addClass('text-muted');
            $('.categoryLimitError').empty();
        }
    });
}

function showModal(category, modalId)
{
    var categoryArray = jQuery.parseJSON(category.replace(/&quot;/g,'"'));

    $(modalId).modal('show');

    $('input[name="categoryNewName"]').val(categoryArray.categoryNewName);
    $('input[name="categoryOldId"]').val(categoryArray.categoryOldId);
    $('input[name="limitAmount"]').val((categoryArray.limitAmount === 0) ? '' : categoryArray.limitAmount);
                
    if(categoryArray.monthlyLimit) {
        $('input[name="monthlyLimit"]').prop('checked', true);
        $('input[name="limitAmount"]').prop('readonly', false);
        $('.limitLabel').removeClass('text-muted');
    }
}

function showLimitInfo()
{
    $('#itemsForm').on('input', function() {
        
        var amount = $('input[name="amount"]').val();
        var category = $('select[name="category"]').val();
        var date = $('input[name="date"]').val();

        if (amount && category) {

            var amountPost = amount;

            $.ajax({
                type: 'POST',
                url: '/expenses/show-limit',
                data: { 
                    amount: amount,
                    category: category,
                    date: date
                },
                sentData: amountPost,
                dataType: 'JSON',
                success: function(limitInfo) {

                    if(limitInfo && limitInfo[0].limit_on) {

                        var totalExpenses = parseFloat(limitInfo[0].expenses_amount) + parseFloat(this.sentData);

                        $('#monthlyLimit').html(limitInfo[0].monthly_limit);
                        $('#previousExpenses').html(limitInfo[0].expenses_amount);
                        $('#remainingAmount').html((limitInfo[0].monthly_limit - limitInfo[0].expenses_amount).toFixed(2));
                        $('#totalExpenses').html(totalExpenses.toFixed(2));

                        if(totalExpenses > limitInfo[0].monthly_limit) {
                            $(".limitInfo").css("background-color", "#ffb3b3");

                        } else {
                            $(".limitInfo").css("background-color", "#aaff80");
                        }

                        $('.limitInfo').prop('hidden', false);

                    } else {

                        $('.limitInfo').prop('hidden', true);
                    }
                }
            });

        } else {

            $('.limitInfo').prop('hidden', true);
        }
    });
}
