
get_data(1);
function get_data(page, filter = {}) {
   limit = 15;
   l_event = "tbody";
   var param = new FormData();
   param.append("_action", "get-monetary-reward");
   param.append("payload", "monetary-reward");
   param.append("limit", limit);
   param.append("pagination", page);
   param.append("filter", JSON.stringify({ filter }));
   var res = run(param);
   var response = JSON.parse(res.responseText);
   let studentsHTML = "";
   let students = response.students.data;
   let month = response.month;
   let year = response.year;
   let totalStudents = response.students.total;
   let credit_card = 0;
   let sim = 0;
   let loan = 0;
   let insurance = 0;
   let gic = 0;
   students.forEach(element => {
      let selected = "";
      if(element.payment_status == "PAID") {
         selected = `<select name="payment_status" data-domain="${element.sub_domain}" data-month ="${month}" data-year="${year}"  class="form-control">
         <option value="UNPAID">UNPAID</option>
         <option value="PAID" selected>PAID</option>
      </select>`;
      }else{
         selected = `<select name="payment_status" data-domain="${element.sub_domain}" data-month ="${month}" data-year="${year}" class="form-control">
         <option value="UNPAID" selected>UNPAID</option>
         <option value="PAID">PAID</option>
      </select>`;
      }
      sim = element.sim_count * 2000;
      studentsHTML += `<tr id="record-undefined">
          <td class="service text-uppercaseservices sub_domain" data-sub_domain="cosmo" data-id="undefined">
             <a type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasEnd"
                aria-controls="offcanvasEnd">${element.organization}</a>
          </td>
          <td class=" text-uppercaseservices">
             <a type="button" class="service" data-bs-toggle="offcanvas" data-bs-target="#offcanvasEnd"
                data-service="gic" aria-controls="offcanvasEnd">
                $ ${element.total_gic}
                </a>
          </td>
          <td class="forex_count">
             <a type="button" class="service" data-bs-toggle="offcanvas" data-bs-target="#offcanvasEnd"
                data-service="forex" aria-controls="offcanvasEnd">
                Rs ${element.forex}
                </a>
          </td>
          <td class="credit_card_count">
             <a type="button" class="service" data-bs-toggle="offcanvas" data-bs-target="#offcanvasEnd"
                data-service="credit_card" aria-controls="offcanvasEnd">
                0
                </a>
          </td>
          <td class="sim_count">
             <a type="button" class="service" data-bs-toggle="offcanvas" data-bs-target="#offcanvasEnd"
                data-service="sim" aria-controls="offcanvasEnd">
                Rs ${sim}</a>
          </td>
          <td class="loan_count">
             <a type="button" class="service" data-bs-toggle="offcanvas" data-bs-target="#offcanvasEnd"
                data-service="loan" aria-controls="offcanvasEnd">
                Rs 0
                </a>
          </td>
          <td class="insurance_count">
             <a type="button" class="service" data-bs-toggle="offcanvas" data-bs-target="#offcanvasEnd"
                data-service="insurance" aria-controls="offcanvasEnd">
                Rs 0
                </a>
          </td>
         <td class="paymentStatus">
            ${selected}
         </td>
          </tr>`;
   });
   if(students.count == 0) {
      studentsHTML = `<tr colspan="8"> No Record </tr>`;
   }
   $('#filterYear').val(year);
   $('#filterMonth').val(month);
   $('tbody').html(studentsHTML);
   $('#pagination').html(response.students.pagination);
}

function filter() {
   var year = document.getElementById("filterYear").value;
   var month = document.getElementById("filterMonth").value;
   return {year: year, month: month };
}

function runFilter(event) {
   event.preventDefault();
   let form = document.getElementById("filterStudents");
   let filters = filter();
   get_data(1, filters);
}


$(document).on('change', 'select[name="payment_status"]', function() {
   let payment_status = $(this).val();
   let sub_domain = $(this).data('domain');
   let month = $(this).data('month');
   let year = $(this).data('year');
   // PAID demo2 05 2023
   changePaymentStatus(payment_status, sub_domain, month, year);
});

function changePaymentStatus(payment_status, sub_domain, month, year) {
   var param = new FormData();
   param.append("_action", "change-payment-status");
   param.append("payload", "monetary-reward");
   param.append("payment_status", payment_status);
   param.append("sub_domain", sub_domain);
   param.append("month", month);
   param.append("year", year);

   // Call your 'run()' function or any other necessary actions
   run(param);
}
