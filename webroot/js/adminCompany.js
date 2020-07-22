$(document).ready(function () {
    $('.submit-admin-company-btn').click(function () {
        var validateResult = $('#admin-company-form').parsley().validate();
        if (validateResult) {
            $('#admin-company-form')[0].submit();
        }
    });
})

function viewAdminCompany(adCompId) {
    if (ajaxing) {
        // still requesting
        return;
    }
    ajaxing = true;
    $('#list-company-overlay').removeClass('hidden');

    $.ajax({
        type: 'GET',
        url: DOMAIN_NAME + '/adminCompanies/view/' + adCompId,
        success: function (resp) {
            if (resp.status == 'success') {
                $('#view-alias').html(resp.data.alias);
                $('#view-short-name').html(resp.data.short_name);
                $('#view-name-vn').html(resp.data.name_vn);
                $('#view-name-en').html(resp.data.name_en);

                $('#view-address-vn').html(resp.data.address_vn);
                $('#view-address-en').html(resp.data.address_en);

                var branch = '';
                if (resp.data.branch_vn) {
                    branch += `${resp.data.branch_vn}<br/>`;
                }
                if (resp.data.branch_jp) {
                    branch += `${resp.data.branch_jp}`;
                }
                $('#view-branch').html(branch);

                $('#view-license').html(resp.data.license);
                $('#view-license-at').html(moment(resp.data.license_at).format('DD-MM-YYYY'));

                $('#view-phone-number').html(resp.data.phone_number);
                $('#view-fax-number').html(resp.data.fax_number);

                $('#view-email').html(resp.data.email);

                $('#view-deputy-name').html(resp.data.deputy_name);
                $('#view-deputy-role-vn').html(resp.data.deputy_role_vn);
                $('#view-deputy-role-jp').html(resp.data.deputy_role_jp);

                $('#view-signer').html(resp.data.signer_name);
                $('#view-signer-role-vn').html(resp.data.signer_role_vn);
                $('#view-signer-role-jp').html(resp.data.signer_role_jp);


                $('#view-incorporate-date').html(moment(resp.data.incorporation_date).format('DD-MM-YYYY'));

                var capital_vn = resp.data.capital_vn.toLocaleString();
                var capital_jp = resp.data.capital_jp.toLocaleString();
                $('#view-capital').html(`${capital_vn} VND (${capital_jp} ¥)`);

                var revenue_vn = resp.data.latest_revenue_vn.toLocaleString();
                var revenue_jp = resp.data.latest_revenue_jp.toLocaleString();
                $('#view-revenue').html(`${revenue_vn} VND (${revenue_jp} ¥)`);

                $('#view-staffs-number').html(resp.data.staffs_number.toLocaleString());

                $('#view-edu-name-vn').html(resp.data.edu_center_name_vn);
                $('#view-edu-name-jp').html(resp.data.edu_center_name_jp);
                $('#view-edu-address-vn').html(resp.data.edu_center_address_vn);
                $('#view-edu-address-en').html(resp.data.edu_center_address_en);

                if (resp.data.created) {
                    $('#view-job-created').html(resp.created);
                } else {
                    $('#view-job-created').html('N/A');
                }
                if (resp.data.created_by_user) {
                    $('#view-job-created-by').html(resp.data.created_by_user.fullname);
                } else {
                    $('#view-job-created-by').html('N/A');
                }

                if (resp.data.modified_by_user) {
                    $('.modified').removeClass('hidden');
                    $('#view-job-modified-by').html(resp.data.modified_by_user.fullname);
                    $('#view-job-modified').html(resp.modified);
                } else {
                    $('.modified').addClass('hidden');
                }

                // toggle modal
                $('#view-ad-company-modal').modal('toggle');
            }
        },
        complete: function () {
            ajaxing = false;
            $('#list-company-overlay').addClass('hidden');
        }
    });
}

function showAddCompanyModal() {
    // reset modal
    $('#add-company-form')[0].reset();
    $('#add-company-form').parsley().reset();
    // show modal
    $('#add-ad-company-modal').modal('toggle');
}

function showEditAdminCompanyModal(adCompId) {
    $('#edit-company-form')[0].reset();
    $('#edit-company-form').parsley().reset();

    if (ajaxing) {
        // still requesting
        return;
    }
    ajaxing = true;
    $('#list-company-overlay').removeClass('hidden');
    $.ajax({
        type: 'GET',
        url: DOMAIN_NAME + '/adminCompanies/view/' + adCompId,
        success: function (resp) {
            // fill data to edit form
            $('#edit-id').val(resp.data.id);
            $('#edit-alias').val(resp.data.alias);
            $('#edit-short-name').val(resp.data.short_name);
            $('#edit-name-vn').val(resp.data.name_vn);
            $('#edit-name-en').val(resp.data.name_en);

            $('#edit-address-vn').val(resp.data.address_vn);
            $('#edit-address-en').val(resp.data.address_en);
            $('#edit-branch-vn').val(resp.data.branch_vn);
            $('#edit-branch-jp').val(resp.data.branch_jp);

            $('#edit-license').val(resp.data.license);
            $('#edit-license-at').val(moment(resp.data.license_at).format('DD-MM-YYYY'));

            $('#edit-phone-number').val(resp.data.phone_number);
            $('#edit-fax-number').val(resp.data.fax_number);
            $('#edit-email').val(resp.data.email);

            $('#edit-deputy-name').val(resp.data.deputy_name);
            $('#edit-deputy-role-vn').val(resp.data.deputy_role_vn);
            $('#edit-deputy-role-jp').val(resp.data.deputy_role_jp);

            $('#edit-signer').val(resp.data.signer_name);
            $('#edit-signer-role-vn').val(resp.data.signer_role_vn);
            $('#edit-signer-role-jp').val(resp.data.signer_role_jp);

            $('#edit-incorporation-date').val(moment(resp.data.incorporation_date).format('DD-MM-YYYY'));
            $('#edit-capital-vn').val(resp.data.capital_vn);
            $('#edit-capital-vn-txt').val(resp.data.capital_vn.toLocaleString());
            $('#edit-capital-jp').val(resp.data.capital_jp);
            $('#edit-capital-jp-txt').val(resp.data.capital_jp.toLocaleString());

            $('#edit-latest-revenue-vn').val(resp.data.latest_revenue_vn);
            $('#edit-latest-revenue-jp').val(resp.data.latest_revenue_jp);
            $('#edit-staffs-number').val(resp.data.staffs_number);

            $('#edit-edu-name-vn').val(resp.data.edu_center_name_vn);
            $('#edit-edu-name-jp').val(resp.data.edu_center_name_jp);
            $('#edit-edu-address-vn').val(resp.data.edu_center_address_vn);
            $('#edit-edu-address-en').val(resp.data.edu_center_address_en);


            // toggle modal
            $('#edit-ad-company-modal').modal('toggle');
        },
        complete: function () {
            ajaxing = false;
            $('#list-company-overlay').addClass('hidden');
        }
    });
}

