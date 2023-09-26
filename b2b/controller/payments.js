function getPayments() {
    let formRequest = new FormData();
    formRequest.append("_action", "getPayments");
    formRequest.append("payload", "payments");
    let res = run(formRequest);
    let pricingHtml = "<tr colspan='9'>Not Found</tr>";
    if (res.responseJSON.data.length > 0) {
      let inc = 1;
      Object.entries(res.responseJSON.data).forEach(function ([key, value]) {
        console.log(value);
        let marketingPerson = '<b>Not Assigned</b>';
        if (value.marketingPersonFname) {
          marketingPerson = value.marketingPersonFname;
        }
        let supportPerson = '<b>Not Assigned</b>';
        if (value.fname) {
          supportPerson = value.fname;
        }
        let reciept = '';
        if(value.reciept_attachment){
          reciept = `<a target="_blank" href="https://team.innerxcrm.com/${value.reciept_attachment}" title="${value.reciept_name}">View</a>`;
        }
        pricingHtml += `<tr id="PaymentsRow_${value.id}">
            <td>${inc++}</td>
            <td>${value.organization}</td>
            <td class="joiningDate">${new Date(value.agreement_signed_at).toLocaleDateString()}</td>
            <td class="contactPerson">${value.companyowner}</td>
            <td class="organizationPhone">${value.organization_phone}</td>
            <td class="marketingPerson">${marketingPerson}</td>
            <td class="supportPerson">${supportPerson}</td>
            <td class="planName"><span class="badge bg-warning p-1 text-dark">${value.title.toUpperCase()}</span></td>
            <td class="planPrice">${value.price}</td>
            <td class="givenDiscount">${value.given_discount}</td>
            <td class="amountPaid">${value.amount}</td>
            <td class="reciept">${reciept}</td>
            <td class="startDate">${new Date(value.from_date).toLocaleDateString()}</td>
            <td class="renewDate">${new Date(value.to_date).toLocaleDateString()}</td>
            <td class="remarks">${value.remarks}</td>
            <td class="paymentMode">${value.payment_mode}</td>
            </tr>`;
      });
    }
    $("#paymentsBody").html(pricingHtml);
  }
  setTimeout(function () {
    getPayments();
  }, 500);

  $(document).on('click',"#export",function () {
    exportTableToExcel('paymentsTable','payments');
  });