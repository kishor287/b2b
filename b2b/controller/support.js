/*

Table headings

*/

var table_cols = [
  'S.no.',
  'Issue Subject',
  'Description',
  'Sub Domain',
];
table_head(table_cols);
get_tickets(1);

function get_tickets(page, filter) {
  var limit = 15;
  var l_event = 'tbody';

  var param = new FormData();
  param.append("_action", "get_tickets");
  param.append("payload", "support");
  param.append("limit", limit);
  param.append("pagination", page);
  param.append("filter", JSON.stringify({
    "search": filter
  }));

  var res = run(param);
  var json = JSON.parse(res.responseText);

  var jHTML = '';
  $.each(json.tickets.data, function(k, v) {
    jHTML += '<tr data-id="' + v['ticket_id'] + '" >';
    jHTML += '<td>' + ((page * limit) - (limit - (k + 1))) + '</td>';
    jHTML += '<td>' + v['subject'] + '</td>';
    //  jHTML += '<td>' + v['chat'] + '</td>';
    
    
    var maxWords = 10;

    var words = v['chat'].split(' ');

// Join the first 'maxWords' words and add "(...)" if there are more words
var truncatedDescription = words.slice(0, maxWords).join(' ');
if (words.length > maxWords) {
  truncatedDescription += ' ...';
}

// Update the description column HTML
jHTML += '<td>' + truncatedDescription + '</td>';


    jHTML += '<td>' + v['sub_domain'] + '</td>';
  jHTML += '<td><select class="form-select form-select-sm developer-select" name="developer" aria-label="">';
var users = json.users.data || [];
var developerNameFromDB = v['developer_name'];
for (var i = 0; i < users.length; i++) {
  var fname = users[i].username;
  var selected = '';
  if (developerNameFromDB === fname) {
    selected = 'selected';
  }

  jHTML += '<option ' + selected + '>' + fname + '</option>';
}

jHTML += '</select></td>';
    jHTML += '<td><select class="form-select form-select-sm status-select" name="status" aria-label="">';

    // Check if ticket_status value exists in the database
    var ticketStatus = v['ticket_status'];
    var options = ['Opened', 'Closed'];
    
    for (var i = 0; i < options.length; i++) {
      if (options[i] === ticketStatus) {
        jHTML += '<option selected>' + ticketStatus + '</option>';
      } else {
        jHTML += '<option>' + options[i] + '</option>';
      }
    }

   jHTML += '</select></td>';
  jHTML += '<td><button class="btn btn-primary btn-sm float-end mx-1 details-button" data-details=\'' + JSON.stringify(v) + '\'>Details</button></td>';
   jHTML += '<td><button class="btn btn-primary btn-sm float-end mx-1 reply-button "> Reply </button></td>';
    jHTML += '<td><button class="btn btn-primary btn-sm float-end mx-1" onclick="assignTicket(this)"> Assign </button></td>';
    jHTML += '</tr>';
  });

  $("tbody").html(jHTML);
  $(".pagination").html(json.pagination);
  $("#count").html(json.total);

  assignTicketUsers(json.users.data);
}

// Assign the users data to the users variable
function assignTicketUsers(usersData) {
  window.users = usersData;
}

function assignTicket(button) {
  var $row = $(button).closest('tr');
  var ticketId = $row.data('id');
  var developerName = $row.find('.developer-select').val().trim();
  var ticket_status = $row.find('.status-select').val();

  var developerEmail = '';
  for (var i = 0; i < users.length; i++) {
    if (users[i].username === developerName) {
      developerEmail = users[i].email;
      break;
    }
  }
// developerName
  $row.find('.developer-column').text();
  $row.find('.status-column').text(ticket_status);

  var formData = new FormData();
  formData.append('_action', 'update');
   formData.append("payload", "support");
  formData.append('ticket_id', ticketId);
  formData.append('developer_name', developerName);
  formData.append('ticket_status', ticket_status);
  formData.append('developerEmail', developerEmail);
 var res = run(formData);
  var json = JSON.parse(res.responseText);

$.ajax({
  url: 'support.php',
  type: 'POST',
  data: {
    _action: 'update',
    ticket_id: ticketId,
    developer_name: developerName,
    ticket_status: ticket_status,
    developerEmail: developerEmail,
    to: developerEmail, // Add the "to" key with the email address
    subject: 'Assigned Ticket Notification',
    content: 'You have been assigned a ticket with ID: ' + ticketId + 'Please check this as soon as possible .'
  },
  success: function(response) {
    console.log(response);
    toastr.success("Updated", "Ticket Assigned Successfully.");
  },
  error: function(xhr, status, error) {
    console.error(error);
  }
});




}

$('tbody').on('click', '.reply-button', function() {
  var ticketId = $(this).closest('tr').data('id');

  $('#replyModal').data('ticket-id', ticketId);
  $('#replyModal').modal('show');
});

$('#sendReplyButton').click(function() {
  var ticketId = $('#replyModal').data('ticket-id');
  var replyText = $('#replyInput').val().trim();

  var formData = new FormData();
  formData.append('_action', 'reply');
  formData.append("payload", "support");
  formData.append('ticket_id', ticketId);
  formData.append('reply', replyText);

 var res = run(formData);
  var json = JSON.parse(res.responseText);
  $.ajax({
    url: 'support.php',
    type: 'POST',
    data: formData,
    processData: false,
    contentType: false,
    success: function(response) {
      console.log(response);
      toastr.success("Reply Sent", "Ticket Updated Successfully.");
      $('#replyModal').modal('hide');
      $('#replyInput').val('');
    },
    error: function(xhr, status, error) {
      console.error(error);
    }
  });
});


$('tbody').on('click', '.details-button', function() {
  var details = $(this).data('details');
  var $modalBody = $('#detailsModalBody');

  // Clear the previous details
  $modalBody.empty();

  // Build the HTML for the ticket details
  var detailsHTML = '<p><strong>Issue Subject:</strong> ' + details.subject + '</p>';
  detailsHTML += '<p><strong>Description:</strong> ' + details.chat + '</p>';
  detailsHTML += '<p><strong>Sub Domain:</strong> ' + details.sub_domain + '</p>';
detailsHTML += '<p><strong>Assigned To:</strong> ' + details.developer_name + '</p>';
detailsHTML += '<p><strong>Ticket Status:</strong> ' + details.ticket_status + '</p>';

  // Add the HTML to the modal body
  $modalBody.html(detailsHTML);

  // Open the modal
  $('#detailsModal').modal('show');
});


