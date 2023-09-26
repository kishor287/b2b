$(document).on('submit', '#pricingPlansForm', function (e) {
  e.preventDefault();

  let formRequest = new FormData(this);
  formRequest.append('_action', 'savePricingPlan');
  formRequest.append('payload', 'pricing-plans');
  run(formRequest);
  $("#staticBackdrop").modal('hide');
  $("#pricingPlansForm")[0].reset();
  getPricingPlans();
});

const getPricingPlans = () => {
  $("#pricingPlansBody").html(`<tr><td colspan="6">Fetching...</td></tr>`);
  let formRequest = new FormData();
  formRequest.append("_action", "getPricingPlans");
  formRequest.append("payload", "pricing-plans");
  let result = run(formRequest);
  let detailsHtml = "";
  if (result.responseJSON.statusCode == 200) {
    if (result.responseJSON.data.length > 0) {
      result.responseJSON.data.forEach((element) => {
        detailsHtml += `<tr id="planRow_${element.id}">  
           <td>
            <button data-id="${element.id}" class="btn rounded-pill btn-sm  btn-icon btn-primary editPlan"><span class="tf-icons bx bx-edit"></span></button>
            <button data-id="${element.id}" class="btn rounded-pill btn-sm  btn-icon btn-danger deletePlan"><span class="tf-icons bx bx-trash"></span></button>
           </td>
            <td class="title">${element.title}</td>
            <td class="price">${element.price}</td>
            <td class="maxDiscount">${element.max_discount}</td>
            <td class="description">${element.description}</td>
            <td>${new Date(element.created_at).toLocaleDateString()}</td>
         </tr>`;
      });
    }
    if (detailsHtml == "") {
      detailsHtml = `<tr><td colspan="6">Not Found</td></tr>`;
    }
    $("#pricingPlansBody").html(detailsHtml);
  }
};

getPricingPlans();

$(document).on('click', '.deletePlan', function () {
  let recordId = $(this).attr('data-id');
  if (!confirm('Are you sure? You wanna delete!')) {
    return false;
  }
  let formRequest = new FormData();
  formRequest.append('_action', 'deletePricingPlan');
  formRequest.append('payload', 'pricing-plans');
  formRequest.append('id', recordId);
  let response = run(formRequest);

  if(response.responseJSON.statusCode == 200){
    $(`#planRow_${recordId}`).remove();
    getPricingPlans();
  }
});


$(document).on('click','.editPlan',function(){

  const recordId = $(this).attr('data-id');
  const title = $(this).closest('tr').find('.title').text();
  const maxDiscount = $(this).closest('tr').find('.maxDiscount').text();
  const description = $(this).closest('tr').find('.description').text();
  const pricing = $(this).closest('tr').find('.price').text();

  $("#planId").val(recordId);
  $("#editTitle").val(title);
  $('#editMaxDiscount').val(maxDiscount);
  $("#editDescription").val(description);
  $('#editPrice').val(pricing);
  
  $("#editPlanModal").modal('show');
});

$(document).on('submit','#updatePricingPlansForm',function(e){

  e.preventDefault();
  let formRequest = new FormData(this);
  formRequest.append('_action', 'updatePricingPlan');
  formRequest.append('payload', 'pricing-plans');
  const result = run(formRequest);
  if(result.responseJSON.statusCode == 200){
    $("#editPlanModal").modal('hide');
    getPricingPlans();
  }
});


