define(
    [
        'Magento_Checkout/js/view/payment/default',
        'ko',
        'jquery'
    ],
    function (Component, ko, $) {
        'use strict';

        return Component.extend({
            defaults: {
                template: 'Customcheckout_Testpayment/payment/testpayment',
                accountHolderName: '',
                accountNumber: ''
            },

            initObservable: function () {
                this._super()
                    .observe(['accountHolderName', 'accountNumber']);

                return this;
            },

            validatePersonName: function () {
                if (!/^[a-zA-Z\s]+$/.test(this.accountHolderName())) {
                    alert('Person Name should only contain alphabetic characters.');
                    return false;
                }
                if (this.accountHolderName().length > 48) {
                    alert('Person Name should not exceed 48 characters.');
                    return false;
                }
                return true;
            },

            validateAccountNumber: function () {
                if (!/^\d{16}$/.test(this.accountNumber())) {
                    alert('Account Number should be 16 digits.');
                    return false;
                }
                return true;
            },

            getData: function () {
                var data = this._super();
                data['additional_data'] = {
                    'account_holder_name': this.accountHolderName(),
                    'account_number': this.accountNumber()
                };
                console.log('Additional Data:', data);
                return data;
            }
        });
    }
);

