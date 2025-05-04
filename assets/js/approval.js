$(document).ready(function () {
  $(".approval-action").click(function (e) {
    e.preventDefault();

    var action = $(this).data("action");
    var role = $(this).data("role");

    var action = $(this).data("action");
    var role = $(this).data("role");

    if (action === "reject_show") {
      const rejectdiv = document.getElementById("reject-form");
      const rejectform = document.getElementById("reject_form");

      if (rejectdiv && rejectform) {
        rejectdiv.classList.remove("d-none");
        rejectform.classList.remove("d-none");

        const textarea = rejectform.querySelector("textarea");
        if (textarea) {
          textarea.scrollIntoView({ behavior: "smooth", block: "center" });
          textarea.focus();
        }
      }
      return;
    }

    // Only run validation logic if the reject form is expected to be visible
    if (
      document.getElementById("reject-form") &&
      document.getElementById("reject_form")
    ) {
      const rejectdiv = document.getElementById("reject-form");

      if (
        !rejectdiv.classList.contains("d-none") &&
        !validateRequiredFields("#reject_form")
      ) {
        return; // Validation failed
      }
    }

    if (!validateRequiredFields("#dispoForm")) {
      // Optional: show alert
      /*Swal.fire({
        title: "Validation Error",
        text: "Please fill all the required fields before proceeding.",
        icon: "error",
        confirmButtonText: "OK",
      });*/
      return; // prevent further action
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
        Swal.fire({
          title: "Processing...",
          html: "Please wait while data is being inserted.",
          allowOutsideClick: false,
          didOpen: () => {
            Swal.showLoading();
          },
        });

        const allowedRoles = ["QA Supervisor", "QA Manager", "Representative"];

        if (role === "QA Engineer" || role === "QA PCO") {
          // Then, upload file attachments and include the ncpr_num
          uploadFileAttachments();
          sendApprovalRequest(action, role); // ENGINEER approval function
        } else if (allowedRoles.includes(role)) {
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

  function validateRequiredFields(scopeSelector) {
    let isValid = true;
    let firstInvalidElement = null;

    // Select required inputs and textareas within the scope
    $(
      `${scopeSelector} input[required], ${scopeSelector} textarea[required]`
    ).each(function () {
      const value = $(this).val().trim();

      if (value === "") {
        isValid = false;
        $(this).addClass("is-invalid");
        if (!firstInvalidElement) firstInvalidElement = $(this);
      } else {
        $(this).removeClass("is-invalid");
      }
    });

    if (!isValid) {
      $("html, body").animate(
        { scrollTop: firstInvalidElement.offset().top - 20 },
        500
      );
      firstInvalidElement.focus();
    }

    return isValid;
  }

  function uploadFileAttachments() {
    let selectedId = $("#modal-id").text(); // Ensure selected ID is correctly retrieved

    // Use the global allFiles array to collect all selected files
    var files = allFiles;

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
    fileFormData.append("ncpr_num", selectedId);

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
    var notes = $("#notes").val(); // Get value of car_no input
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

    var interventionData = getIntervention();

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

        ...interventionData,
      },
      dataType: "json", // Expect JSON response
      success: function (response) {
        if (response.status === "success") {
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
          //this is for debugging
          //Swal.fire("Error", response.message, "error");
        }
      },
      error: function (xhr, status, error) {
        console.error("AJAX Error:", error, xhr.responseText);
        alert("An Error Occurred", "A system error has occurred. Please contact support for assistance. Check the console for more details.", "error");
      },
    });
  }

  function sendSPMGRApproval(action, role) {
    let selectedId = $("#modal-id").text(); // Ensure selected ID is correctly retrieved

    let data = {
      action: action,
      role: role,
      ncpr_num: selectedId,
    };

    if (role === "Representative") {
      var reason = $("#rejection_reason").val();
      data.reason = reason;
    }

    $.ajax({
      url: "approval.php",
      type: "POST",
      data: data,
      success: function (response) {
        // Handle success response
        if (response.status === "success") {
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
            }*/
      error: function (xhr, status, error) {
        console.error("AJAX Error:", error, xhr.responseText);
        alert("An Error Occurred", "A system error has occurred. Please contact support for assistance. Check the console for more details.", "error");
      },
    });
  }

  function getIntervention() {
    var result = {};

    // Collect values only if at least one is selected/filled

    var action_taken = $("input[name='actions_taken[]']:checked");
    if (action_taken.length)
      result.action_taken = action_taken
        .map(function () {
          return $(this).val();
        })
        .get();

    var process_dispo = $("input[name='process_dispo[]']:checked");
    if (process_dispo.length)
      result.process_dispo = process_dispo
        .map(function () {
          return $(this).val();
        })
        .get();

    var aff_process = $("input[name='affected_process']").val();
    if (aff_process) result.affected_process = aff_process;

    var further_eval = $("input[name='F1']:checked").val();
    if (further_eval) result.further_eval = further_eval;

    var resumption = $("input[name='resumption_reason[]']:checked");
    if (resumption.length)
      result.resumption = resumption
        .map(function () {
          return $(this).val();
        })
        .get();

    var otherResumption = $("input[name='other_resumption']").val();
    if (otherResumption) result.other_resumption = otherResumption;

    var processInstruction = $("textarea[name='process_instruction']").val(); // fixed to textarea, not input
    if (processInstruction) result.process_instruction = processInstruction;

    var instru_details = $("input[name='instructions_detail[]']:checked");
    if (instru_details.length)
      result.instru_details = instru_details
        .map(function () {
          return $(this).val();
        })
        .get();

    var doc_alert = $("input[name='document_alert_s']").val();
    if (doc_alert) result.document_alert_s = doc_alert;

    var others = $("input[name='other_specify_s']").val();
    if (others) result.other_specify_s = others;

    var docu_rev = $("input[name='documents_revision[]']:checked");
    if (docu_rev.length)
      result.docu_rev = docu_rev
        .map(function () {
          return $(this).val();
        })
        .get();

    var Sign_Date = $("input[name='released_by']").val();
    if (Sign_Date) result.released_by = Sign_Date;

    // Return the result if at least one value exists
    return Object.keys(result).length ? result : false;
  }
});
