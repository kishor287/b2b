const getGicDetails = (page = 1,filter = {}) => {
  $("#gicTbody").html(
    `<tr><td colspan="10">Fetching...</td></tr>`
  );
  let formRequest = new FormData();
  formRequest.append("_action", "getGicDetails");
  formRequest.append("payload", "save-gic");
  formRequest.append("page", page);
  formRequest.append("filter",JSON.stringify(filter));
  let result = run(formRequest);
  let detailsHtml = "";
  if (result.responseJSON.statusCode == 200) {
    if(result.responseJSON.data.length > 0){
      result.responseJSON.data.forEach((element) => {
        console.log(element);
        detailsHtml += `<tr id  ="gicDetailRow_${element.id}">  
         <td>
          <button data-id="${element.id}" class="btn rounded-pill btn-sm  btn-icon btn-primary editGicDetails"><span class="tf-icons bx bx-edit"></span></button>
          <button data-id="${element.id}" class="btn rounded-pill btn-sm  btn-icon btn-danger deleteGicDetail"><span class="tf-icons bx bx-trash"></span></button>
         </td>
         <td class="organization_id" data-id="${element.organization_id}">${element.organization}</td>
         <td>${element.fname}</td>
         <td>${new Date(element.created_at).toLocaleDateString()}</td>
         <td class="email_id">${element.organization_email}</td>
         <td class="student_name">${element.student_name}</td>
         <td class="passport_number">${element.passport_number}</td>
         <td class="bank">${element.bank}</td>
         <td class="gic_acc_number">${element.gic_acc_number}</td>
         <td class="gic_reference_number_for_simpli">${element.gic_reference_number_for_simpli}</td>
         <td class="amount">${element.amount}</td>
         <td class="commission">${element.commision}</td>
       </tr>`;
      });
    }
    if (detailsHtml == "") {
      detailsHtml = `<tr><td colspan="10">Not Found</td></tr>`;
    }
    $("#gicTbody").html(detailsHtml);
    $("#pagination").html(result.responseJSON.pagination);
  }
};

const getAgencies = () => {
  let formRequest = new FormData();
  formRequest.append('_action','getOrganizations');
  formRequest.append('payload','save-gic');
  let res = run(formRequest);
  res  = JSON.parse(res.responseText);
  let agenciesOption = '';
  res.data.forEach(element => {
      agenciesOption += `<option value="${element.id}">${element.organization}</option>`;
  });
  $("#organizationId").html(agenciesOption);
//   $('#organizationId').select2({
//       theme: "bootstrap-5",
//       width: $(this).data('width') ? $(this).data('width') : $(this).hasClass('w-100') ? '100%' : 'style',
//       placeholder: $(this).data('placeholder'),
//  });
}
// get all organizations from lead table 
getAgencies();
// get submitted gics 
getGicDetails();

function getCurrentPage() {
  if ($(".page-item.active").text() !== "") {
    return $(".page-item.active").text();
  } else {
    return 1;
  }
}

$("body").on("click", "#_pagination", function () {
  getGicDetails($(this).attr("data-id"));
});

// Edit Details
$(document).on("click",".editGicDetails", function () {
  let recordId = $(this).attr('data-id');
  let organizationId = $(this).closest("tr").find(".organization_id").data('id');
  console.log(organizationId);
  let emailId = $(this).closest("tr").find(".email_id").text();
  let studentName = $(this).closest("tr").find(".student_name").text();
  let passportNumber = $(this).closest("tr").find(".passport_number").text();
  let bank = $(this).closest("tr").find(".bank").text();
  let gicAccNumber = $(this).closest("tr").find(".gic_acc_number").text();
  let gicReferenceNumberForSimpli = $(this).closest("tr").find(".gic_reference_number_for_simpli").text();
  let amount = $(this).closest("tr").find(".amount").text();
  let commission = $(this).closest("tr").find(".commission").text();

  $("#organizationId").val(organizationId);
  $("#recordId").val(recordId);
  $("#studentEmailId").val(emailId);
  $("#studentName").val(studentName);
  $("#passportNumber").val(passportNumber);
  $("#bank").val(bank);
  $("#gicAccountNumber").val(gicAccNumber);
  $("#gicReferenceNumberForSimpli").val(gicReferenceNumberForSimpli);
  $("#amount").val(amount);
  $("#commission").val(commission);
  $("#editGicDetailsModal").modal("show");
});


$(document).on("submit", "#updateAgency", function (e) {
  e.preventDefault();
  let formRequest = new FormData(this);
  let result = run(formRequest);
  if (result.responseJSON.statusCode == 200) {
    getGicDetails(getCurrentPage());
    $("#editGicDetailsModal").modal("hide");
  }
});


$(document).on('click','.deleteGicDetail',function(){
  if(confirm('Are you sure! You want to delete this record?')){
    let id = $(this).data('id');
    let formRequest = new FormData();
    formRequest.append('id',id);
    formRequest.append('payload','save-gic');
    formRequest.append('_action','deleteGicDetail');
    let res = run(formRequest);
    if(res.responseJSON.statusCode == 200){
      getGicDetails();
    }
  }

});


$(document).on('change','#daterange',function(){
    let dateRange = $(this).val();
    getGicDetails(getCurrentPage(),{dateRange:dateRange})
});

