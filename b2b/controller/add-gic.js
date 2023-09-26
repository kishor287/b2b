const getAgencies = () => {
  let formRequest = new FormData();
  formRequest.append("_action", "getOrganizations");
  formRequest.append("payload", "save-gic");
  let res = run(formRequest);
  res = JSON.parse(res.responseText);
  let agenciesOption = "";
  res.data.forEach((element) => {
    agenciesOption += `<option value="${element.id}">${element.organization}</option>`;
  });
  $("#agencyInput").html(agenciesOption);
  $("#agencyInput").select2({
    theme: "bootstrap-5",
    width: $(this).data("width")
      ? $(this).data("width")
      : $(this).hasClass("w-100")
      ? "100%"
      : "style",
    placeholder: $(this).data("placeholder"),
  });
};

$(document).on("keyup", ".nonTextInput", function () {
  let value = $(this).val();
});

const cannotTypeNumber = (event) => {
  const keyCode = event.keyCode || event.which;
  if (
    keyCode === 8 || // Backspace
    keyCode === 9 || // Tab
    keyCode === 46 || // Delete
    (keyCode >= 37 && keyCode <= 40) // Arrow keys
  ) {
    return;
  }
  if (keyCode < 48 || keyCode > 57) {
    event.preventDefault(); // Prevent input of non-numeric characters
  }
};

getAgencies();

$(document).on("submit", "#saveAgency", function (e) {
  e.preventDefault();
  let formRequest = new FormData(this);
  let result = run(formRequest);
  if (result.responseJSON.statusCode == 200) {
    $("#saveAgency")[0].reset();
  }
});
