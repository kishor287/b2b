const getLoanSimDetails = (page = 1,filter = {}) => {
  $("#loanSimTbody").html(
    `<tr><td colspan="10">Fetching...</td></tr>`
  );
  let formRequest = new FormData();
  formRequest.append("_action", "getLoanSimDetails");
  formRequest.append("payload", "add-sim-and-loan");
  formRequest.append("page", page);
  formRequest.append("filter",JSON.stringify(filter));
  let result = run(formRequest);
  let detailsHtml = "";
  if (result.responseJSON.statusCode == 200) {
    let key = 1;
    if(result.responseJSON.data.length > 0){
      result.responseJSON.data.forEach((element) => {
        let barCode = '--';
        if(element.sim_card_barcode){
          barCode = `<a href="https://team.innerxcrm.com/${element.sim_card_barcode}" download>View</a>`;
        }
        detailsHtml += `<tr id="agentBaseDetailRow_${element.id}">  
         <td>
          <button data-id="${element.id}" class="btn rounded-pill btn-sm  btn-icon btn-primary editSimLoanBtn"><span class="tf-icons bx bx-edit"></span></button>
          <button data-id="${element.id}" class="btn rounded-pill btn-sm  btn-icon btn-danger deleteSimLoanBtn"><span class="tf-icons bx bx-trash"></span></button>
         </td>
         <td class="date" data-date="${element.created_at}">${new Date(element.created_at).toLocaleDateString()}</td>
         <td class="agent_name">${element.agent_name}</td>
         <td class="student_name">${element.student_name}</td>
         <td class="contact_number">${element.contact_number}</td>
         <td class="location">${element.location}</td>
         <td class="amount">${element.amount}</td>
         <td class="status">${element.status}</td>
         <td class="sim_card_quantity">${element.sim_card_quantity}</td>
         <td class="sim_card_number">${element.sim_card_number}</td>
         <td class="sim_card_barcode">${barCode}</td>
       </tr>`;
      });
    }
    if (detailsHtml == "") {
      detailsHtml = `<tr><td colspan="10">Not Found</td></tr>`;
    }
    $("#loanSimTbody").html(detailsHtml);
    $("#pagination").html(result.responseJSON.pagination);
  }
};

getLoanSimDetails();

function getCurrentPage() {
  if ($(".page-item.active").text() !== "") {
    return $(".page-item.active").text();
  } else {
    return 1;
  }
}

$("body").on("click", "#_pagination", function () {
  getLoanSimDetails($(this).attr("data-id"));
});

// Edit Details
$(document).on("click",".editSimLoanBtn", function () {
  let recordId = $(this).attr('data-id');
  let agentName = $(this).closest("tr").find(".agent_name").text();
  let date = $(this).closest("tr").find(".date").data('date');
  let studentName = $(this).closest("tr").find(".student_name").text();
  let contactNumber = $(this).closest("tr").find(".contact_number").text();
  let location = $(this).closest("tr").find(".location").text();
  let amount = $(this).closest("tr").find(".amount").text();
  let status = $(this).closest("tr").find(".status").text();
  let simCardQuantity = $(this).closest("tr").find(".sim_card_quantity").text();
  let simCardNumber = $(this).closest("tr").find(".sim_card_number").text();

  $("#agentName").val(agentName);
  $("#date").val(date);
  $("#recordId").val(recordId);
  $("#location").val(location);
  $("#studentName").val(studentName);
  $("#contactNumber").val(contactNumber);
  $("#amount").val(amount);
  $("#status").val(status);
  $("#simCardQuantity").val(simCardQuantity);
  $("#simCardNumber").val(simCardNumber);

  $("#editLoanSimModal").modal("show");
});


$(document).on("submit", "#updateSimLoan", function (e) {
  e.preventDefault();
  let formRequest = new FormData(this);
  let result = run(formRequest);
  if (result.responseJSON.statusCode == 200) {
    getLoanSimDetails(getCurrentPage());
    $("#editLoanSimModal").modal("hide");
  }
});


$(document).on('click','.deleteSimLoanBtn',function(){
  if(confirm('Are you sure! You want to delete this record?')){
    let id = $(this).data('id');
    let formRequest = new FormData();
    formRequest.append('id',id);
    formRequest.append('payload','add-sim-and-loan');
    formRequest.append('_action','deleteSimLoan');
    let res = run(formRequest);
    if(res.responseJSON.statusCode == 200){
      getLoanSimDetails();
    }
  }

});


$(document).on('change','#daterange',function(){
    let dateRange = $(this).val();
    getLoanSimDetails(getCurrentPage(),{dateRange:dateRange})
});

