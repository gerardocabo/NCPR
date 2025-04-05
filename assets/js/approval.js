$(document).ready(function () {
  $(".approval-action").click(function (e) {
    e.preventDefault();

    // Check if all required inputs (including textareas) are filled before continuing
    var isValid = true;
    var firstInvalidElement = null; // To store the first invalid field

    // Check required inputs
    $("input[required]").each(function () {
      if ($(this).val() === "") {
        isValid = false;
        $(this).addClass("is-invalid"); // Optionally, add a class for styling
        if (!firstInvalidElement) {
          firstInvalidElement = $(this); // Set first invalid element
        }
      } else {
        $(this).removeClass("is-invalid");
      }
    });

    // Check required textareas
    $("textarea[required]").each(function () {
      if ($(this).val().trim() === "") {
        isValid = false;
        $(this).addClass("is-invalid"); // Optionally, add a class for styling
        if (!firstInvalidElement) {
          firstInvalidElement = $(this); // Set first invalid element
        }
      } else {
        $(this).removeClass("is-invalid");
      }
    });

    if (!isValid) {
      // Scroll to the first invalid field and focus on it
      $("html, body").animate(
        {
          scrollTop: firstInvalidElement.offset().top - 20, // Adjust for better visibility
        },
        500
      );

      firstInvalidElement.focus(); // Focus the first invalid field
      // Show a message if any required field is empty
      /*Swal.fire({
        title: "Validation Error",
        text: "Please fill all the required fields before proceeding.",
        icon: "error",
        confirmButtonText: "OK",
      });*/
      return; // Stop the rest of the code from executing
    }

    var action = $(this).data("action");
    var role = $(this).data("role");
    var selectedId; // Declare variable before the condition

    // If action is "cancel" and role is "QA Engineer", use #view-ncpr-num
    if (action === "cancel" && role === "QA Engineer") {
      selectedId = $("#view-ncpr-num").text();
    } else {
      selectedId = $("#modal-id").text(); // Default selector
    }

    Swal.fire({
      title: "Are you sure?",
      text: "You are about to " + action + " this NCPR?",
      icon: "warning",
      showCancelButton: true,
      confirmButtonColor: "#3085d6",
      cancelButtonColor: "#d33",
      confirmButtonText: "Yes, approve it!",
      cancelButtonText: "Cancel",
    }).then((result) => {
      if (result.isConfirmed) {
        if (role === "QA Engineer" && action !== "cancel") {
          // Then, upload file attachments and include the ncpr_num
          uploadFileAttachments(selectedId);
          sendApprovalRequest(action, role); // ENGINEER approval function
        } else if (role === "QA Manager" || "Representative") {
          sendSPMGRApproval(action, role); // MANAGER/SUPERVISOR approval function
        } else if (action === "cancel" || "reject") {
          sendSPMGRApproval(action, role); // MANAGER/SUPERVISOR approval function
        } else {
          Swal.fire(
            "Error",
            "You do not have permission to approve this request.",
            "error"
          );
        }
      }
    });
  });

  function uploadFileAttachments(ncpr_num) {
    // Collect all file attachments and append them to FormData
    var files = $("input[name='attachments[]']")[0].files;

    // If no files selected, simply exit the function
    if (files.length === 0) {
      return; // Exit if no files
    }

    // Create a new FormData object for file attachments
    var fileFormData = new FormData();

    for (var i = 0; i < files.length; i++) {
      fileFormData.append("attachments[]", files[i]);
    }

    // Append the ncpr_num to the FormData so it is sent along with the files
    fileFormData.append("ncpr_num", ncpr_num);

    // Send the file attachments using AJAX
    $.ajax({
      url: "insert_fileUpload.php", // Server-side script to handle file uploads
      type: "POST",
      data: fileFormData,
      processData: false, // Prevent jQuery from processing the data
      contentType: false, // Let the browser set the content type for file uploads
      success: function (response) {
        try {
          var parsedResponse = JSON.parse(response);
          if (parsedResponse.status !== "success") {
            alert("File upload failed. Please try again.");
          }
        } catch (e) {
          alert("File upload failed. Invalid server response.");
        }
      },
      error: function (xhr, status, error) {
        Swal.fire("Error", "File upload failed. Check console.", "error");
      },
    });
  }

  function sendApprovalRequest(action, role) {
    let selectedId = $("#modal-id").text(); // Ensure selected ID is correctly retrieved

    var Containment = $("#containment").val();
    var causes = [];
    $("input[name='cause[]']:checked").each(function () {
      causes.push($(this).val());
    });

    //inputs
    var idNo = $("input[name='id_no']").val().trim();
    var name = $("input[name='name']").val().trim();
    var carNo = $("input[name='car_no']").val(); // Get value of car_no input
    var scarNo = $("input[name='scar_no']").val().trim(); // Get value of car_no input
    var DA = $("input[name='document_alert']").val().trim(); // Get value of car_no input
    var notes = $("input[name='impact_analysis']").val(); // Get value of car_no input
    var contactperson = $("input[name='contact_person']").val().trim(); // Get value of car_no input
    var otherSpecify = $("input[name='other_specify']").val().trim(); // Get value of car_no input
    var yieldOff = $("input[name='yield_off']").val().trim(); // Get value of car_no input
    var regradeDA = $("input[name='da_no']").val().trim(); // Get value of car_no input
    var reworkDA = $("input[name='rework_da_no']").val().trim(); // Get value of car_no input
    var wisnum = $("input[name='wis_no']").val().trim(); // Get value of car_no input
    var repairDA = $("input[name='repair_DA']").val().trim(); // Get value of car_no input
    var scrap_amount = $("input[name='scrap_amount']").val().trim(); // Get value of car_no input
    var shipDate = $("input[name='shipment_date']").val().trim(); // Get value of car_no input

    //radio_inputs
    var correctiveAction = $("input[name='corrective_action']:checked").val();
    var pff = $("input[name='potential_failure']:checked").val();
    var bdReport = $("input[name='bd_report']:checked").val();
    var mrb = $("input[name='mrb']:checked").val();
    var custApp = $("input[name='customer_approval']:checked").val();

    //checkboxes
    // ✅ Collect independent checkboxes into an array
    var independent_checkbox = [];

    var car = $("input[name='car']:checked").val();
    if (car) independent_checkbox.push(car);

    var scar = $("input[name='scar']:checked").val();
    if (scar) independent_checkbox.push(scar);

    var affectedBusiness = $("input[name='affected_business']:checked").val();
    if (affectedBusiness) independent_checkbox.push(affectedBusiness);

    var otherInstructions = $("input[name='other_instructions']:checked").val();
    if (otherInstructions) independent_checkbox.push(otherInstructions);

    var dispoFrom = [];
    $("input[name='dispo_from[]']:checked").each(function () {
      dispoFrom.push($(this).val());
    });
    var IARA = [];
    $("input[name='impact_analysis[]']:checked").each(function () {
      IARA.push($(this).val());
    });

    var prod_dispo = [];
    $("input[name='product_dispo[]']:checked").each(function () {
      prod_dispo.push($(this).val());
    });

    console.log("Sending AJAX request...");
    $.ajax({
      url: "approval.php",
      type: "POST",
      data: {
        action: action,
        role: role,
        ncpr_num: selectedId,

        //inputs
        containment: Containment,
        cause: causes,
        id_no: idNo,
        name: name,
        car_no: carNo,
        scar_no: scarNo,
        document_alert: DA,
        notes: notes,
        contact_person: contactperson,
        other_specify: otherSpecify,
        yield_off: yieldOff,
        da_no: regradeDA,
        rework_da_no: reworkDA,
        wis_no: wisnum,
        repair_DA: repairDA,
        scrap_amount: scrap_amount,
        shipment_date: shipDate,

        //arrays of checkboxes
        independents: independent_checkbox,
        dispo_from: dispoFrom,
        IARA: IARA,
        product_dispo: prod_dispo,

        //i did forgot the radios
        corrective_action: correctiveAction,
        potential_failure: pff,
        bd_report: bdReport,
        mrb: mrb,
        customer_approval: custApp,
      },
      dataType: "json", // Expect JSON response
      beforeSend: function () {
        console.log("Sending AJAX request...");
      },
      success: function (response) {
        console.log("Raw response:", response);

        if (response.status === "success") {
          console.log("Parsed JSON:", response);

          Swal.fire({
            title: "Success",
            text: response.message,
            icon: "success",
            confirmButtonText: "OK",
          }).then(() => {
            // Hide the Swal modal
            $(".swal2-container").fadeOut(200, function () {
              $(this).remove(); // Remove Swal2 container after fadeOut
            });
            // Clear the form
            $("#dispoForm")[0].reset();
            // Hide the Bootstrap modal
            $("#dispoModal").modal("hide"); // Ensure you replace #viewModal with your actual modal ID
            // ✅ Refresh the DataTable securely
            if (typeof refreshNcprTable === "function") {
              refreshNcprTable();
            }
          });
        } else {
          console.error("Error from server:", response.message);
          Swal.fire("Error", response.message, "error");
        }
      },
      error: function (xhr, status, error) {
        console.error("AJAX Error:", error, xhr.responseText);
        Swal.fire("Error", "AJAX request failed. Check console.", "error");
      },
    });
  }

  function sendSPMGRApproval(action, role) {
    let selectedId = $("#modal-id").text(); // Ensure selected ID is correctly retrieved
    console.log("Sending AJAX request for MANAGER/SUPERVISOR...");

    let viewmodalID = $("#view-ncpr-num").text(); // Ensure selected ID is correctly retrieved
    if(viewmodalID){console.log("Sending AJAX request for cancel...");}

    if (role === "QA Engineer") {
      selectedId = viewmodalID;
    }

    $.ajax({
      url: "approval.php",
      type: "POST",
      data: {
        action: action,
        role: role,
        ncpr_num: selectedId,
      },
      success: function (response) {
        console.log("Response received:", response);
        // Handle success response
        if (response.status === "success") {
          console.log("Parsed JSON:", response);

          Swal.fire({
            title: "Success",
            text: response.message,
            icon: "success",
            confirmButtonText: "OK",
          }).then(() => {
            // Hide the Swal modal
            $(".swal2-container").fadeOut(200, function () {
              $(this).remove(); // Remove Swal2 container after fadeOut
            });

            // Hide the Bootstrap modal
            $("#dispoModal").modal("hide"); // Ensure you replace #viewModal with your actual modal ID
            // ✅ Refresh the DataTable securely
            if (typeof refreshNcprTable === "function") {
              refreshNcprTable();
            }
          });
        } else {
          console.error(
            "Error from server:",
            response.status,
            response.message,
            response.stack || "No stack trace available"
          );
          Swal.fire("Error", response.message, "error");
        }
      },
      /*error: function (xhr, status, error) {
                console.error("Error:", error);
            }*/ error: function (xhr, status, error) {
        console.error("AJAX Error:", error, xhr.responseText);
        Swal.fire("Error", "AJAX request failed. Check console.", "error");
      },
    });
  }
});
