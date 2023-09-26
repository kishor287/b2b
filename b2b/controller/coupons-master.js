/*
Subdomain
*/

dir();
function dir() {
  var param = new FormData();
  param.append("_action", "dir");
  param.append("payload", "coupons-master");
  var res = run(param);
  var json = JSON.parse((res.responseText));
  jHTML = ''
  jHTML += '<option value="" id="_org">Select all</option>'
  $.each(json.data, function (k, v) {
    //console.log(v);
    jHTML += '<option value="' + v['id'] + '" >' + v['organization'] + '</option>'
  })
  $("#_dir").html(jHTML);

}
$('#_org').click(function () {
  $('#_dir option').prop('selected', true);
});

$('#_dir').click(function () {
  var organizationValues = $('#_dir').val();
  //console.log(organizationValues);
});

/*
Save
*/
$('#coupon_form').submit(function (e) {
  e.preventDefault();

  var form = $(this)[0];
  var param = new FormData(form);

  param.append("_action", "save");
  param.append("payload", "coupons-master");

  var res = run(param);

  var json = JSON.parse((res.responseText));
  if (json['status'] === 1) {
    $('#coupon_form')[0].reset();
    toastr.success("Added", "Data Inserted Successfully.")

    // Update the table with the preview data
    var previewTable = $('#previewTable tbody');
    var row = $('<tr></tr>');
    row.append($('<td></td>').text(json['data']['banner_image']));
    row.append($('<td></td>').text(json['data']['banner_text']));
    previewTable.append(row);


  } else {
    toastr.error("Error", "Internal Server Error")

  }

});

get_data(1);
function get_data(page, filter = {}) {
  limit = 15;
  l_event = "tbody";
  var param = new FormData();
  param.append("_action", "index");
  param.append("payload", "coupons-master");
  param.append("limit", limit);
  param.append("pagination", page);
  //  param.append("filter", JSON.stringify({ filter }));
  var res = run(param);
  var response = JSON.parse(res.responseText);
  let couponsHTML = "";
  let coupons = response.coupons.data;
  let gic = '';
  let forex = '';
  let sim = '';
  let loan = '';
  let insurance = '';
  let credit_card = '';
  let image = '';
  coupons.forEach((coupon) => {
    if (coupon.gic == 1) {
      gic = '<div class="badge btn btn-primary btn-sm">GIC</div>';
    }
    if (coupon.forex == 1) {
      forex = '<div class="badge btn btn-primary btn-sm ms-1"> FOREX</div>';
    }
    if (coupon.sim == 1) {
      sim = '<div class="badge btn btn-primary btn-sm ms-1"> SIM</div>';
    }
    if (coupon.loan == 1) {
      loan = '<div class="badge btn btn-primary btn-sm ms-1"> LOAN</div>';
    }
    if (coupon.insurance == 1) {
      insurance = '<div class="badge btn btn-primary btn-sm ms-1"> INSURANCE</div>';
    }
    if (coupon.credit_card == 1) {
      credit_card = '<div class="badge btn btn-primary btn-sm ms-1"> CREDIT CARD</div>';
    }
    if (coupon.banner_image != '') {
      image = '<a class="badge btn btn-primary btn-sm" href="/uploads/coupon/' + coupon.banner_image + '" target="_blank">Preview </a>';
    } else {
      image = coupon.banner_text;
    }
    couponsHTML += `
      <tr>
      <td><button class="badge btn btn-warning btn-sm"><i class="bx bx-edit"></i></td>
      <td>${coupon.id}</td>
      <td>$ ${coupon.amount}</td>
      <td>${gic}  ${forex} ${sim} ${loan}${insurance}${credit_card} </td>
      <td>${image}</td>
      <td>${coupon.created}</td>
      </tr>`;
  });
  let forexSubServices = response.forexBanks;
  let gicSubServices = response.gicBanks;

  let forexSubServicesHTML = '';
  let gicSubServicesHTML = '';

  forexSubServices.forEach((forexSubService) => {
    forexSubServicesHTML += `
    <div class="form-check form-switch">
    <input class="form-check-input" type="checkbox" id="${forexSubService.id}" name="gicSubServices[]" value="${forexSubService.id}">

    <label class="form-check-label" for="${forexSubService.id}">${forexSubService.name}</label>
    </div>
    `;
  });
  gicSubServices.forEach((gicSubService) => {
    gicSubServicesHTML += `
    <div class="form-check form-switch">
    <input class="form-check-input" type="checkbox" id="${gicSubService.id}" name="forexSubServices[]" value="${gicSubService.id}">

    <label class="form-check-label" for="${gicSubService.id}">${gicSubService.name}</label>
    </div>
    `;
  });
  $('.forexsubServices').html(forexSubServicesHTML);
  $('.gicsubServices').html(gicSubServicesHTML);
  $('tbody').html(couponsHTML);
  //  $('#pagination').html(response.students.pagination);
}

$(document).on('change', '.services', function () {
  let service = $(this).val();
  if ($(this).is(":checked")) {
    $(`.${service}subServices`).show();
  } else {
    $(`.${service}subServices`).hide();
  }

});