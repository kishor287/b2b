$(document).on("submit", "#agentBase", function (e) {
  e.preventDefault();
  let formRequest = new FormData(this);
  let result = run(formRequest);
  if (result.responseJSON.statusCode == 200) {
    $("#agentBase")[0].reset();
  }
});


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
  $(".organizationId").html(agenciesOption);
    $('.organizationId').select2({
      theme: "bootstrap-5",
      width: $(this).data('width') ? $(this).data('width') : $(this).hasClass('w-100') ? '100%' : 'style',
      placeholder: $(this).data('placeholder'),
 });
}
getAgencies();
