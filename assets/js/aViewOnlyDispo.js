//viewonly dispo modal script
$(document).ready(function () {
  $("#ncprTable tbody").on("click", ".dispo-btn", function () {
    var ncprNum = $(this).data("id");
    $("#modal-id").text(ncprNum); // Display ID inside modal

    $.ajax({
      url: "fetch_dispo_details.php", // New PHP script to fetch dispo_id
      method: "POST",
      data: {
        ncpr_num: ncprNum,
      },
      dataType: "json",
      success: function (response) {
        // Log the full response for debugging
        console.log("Encoded JSON response:", response);
        if (response.error === "No matching records found") {
          Swal.fire({
            icon: "info", // Soft message icon
            title: "No Records Found",
            text: "There are no matching records. Please check your input and try again.",
            confirmButtonColor: "#3085d6",
          }).then(() => {
            $("#dispoModal").modal("hide"); // Close modal after user clicks "OK"
          });
          return;
        } else {
          console.log("Dispo ID found. Disabling inputs.", response);

          // Populate fields with existing data
          $("#modal-id").text(response.ncpr_num);

          //$('#containment').val(response.containment);
          $("#containment").text(response.containment); // Sets the text content
          $("#non-conformance").text(response.non_conformance);
          $(
            'input[name="corrective_action"][value="' +
              response.corrective_action +
              '"]'
          ).prop("checked", true);
          $(
            'input[name="potential_failure"][value="' + response.pff + '"]'
          ).prop("checked", true);

          // Populate multiple checkboxes for cause of non-conformance
          $('input[name="cause[]"]').each(function () {
            let checkboxValue = $(this).val(); // Get the value of each checkbox
            let isChecked = response.checkboxes.some(
              (cb) => cb.checkbox_name === checkboxValue
            );
            $(this).prop("checked", isChecked);
          });

          // Populate ID, name, CAR, SCAR fields
          $("#id_no").text(response.id_no);
          $("#name").text(response.name);
          // Check CAR and SCAR based on the checkboxes array from the response
          $('input[name="car"]').prop(
            "checked",
            response.checkboxes.some((cb) => cb.checkbox_name === "CAR")
          );
          $('input[name="scar"]').prop(
            "checked",
            response.checkboxes.some((cb) => cb.checkbox_name === "SCAR")
          );
          $("#car_no").text(response.car_no);
          $("#scar_no").text(response.scar_no);

          // sets checked for Dispo Required from
          // Populate dispo checkboxes
          $('input[name="dispo_from[]"]').each(function () {
            let checkboxValue = $(this).val(); // Get the value of each checkbox
            let isChecked = response.checkboxes.some(
              (cb) => cb.checkbox_name === checkboxValue
            );
            $(this).prop("checked", isChecked);
          });

          // Populate IARA checkboxes
          $('input[name="impact_analysis[]"]').each(function () {
            let checkboxValue = $(this).val(); // Get the value of each checkbox
            let isChecked = response.checkboxes.some(
              (cb) => cb.checkbox_name === checkboxValue
            );
            $(this).prop("checked", isChecked);
          });

          $('input[name="affected_business"]').prop(
            "checked",
            response.checkboxes.some((cb) => cb.checkbox_name === "CAR")
          );
          $('input[name="other_instructions"]').prop(
            "checked",
            response.checkboxes.some((cb) => cb.checkbox_name === "SCAR")
          );

          // Set BD report and MRB radio buttons
          $('input[name="bd_report"][value="' + response.bd_report + '"]').prop(
            "checked",
            true
          );
          $('input[name="mrb"][value="' + response.mrb + '"]').prop(
            "checked",
            true
          );
          $(
            'input[name="customer_approval"][value="' +
              response.customer_approval +
              '"]'
          ).prop("checked", true); // Added this

          // Populate product disposition checkboxes
          $('input[name="product_dispo[]"]').each(function () {
            let checkboxValue = $(this).val(); // Get the value of each checkbox
            let isChecked = response.checkboxes.some(
              (cb) => cb.checkbox_name === checkboxValue
            );
            $(this).prop("checked", isChecked);
          });

          // Populate text fields
          $("#yield_off").text(response.yield_off || "");
          $("#da_no").text(response.da_no || "");
          $("#rework_da_no").text(response.rework_da_no || "");
          $("#wis_no").text(response.wis_no || "");
          $("#scrap_amount").text(response.scrap_amount || "");
          $("#shipment_date").text(response.shipment_date || "");
          $("#document_alert").text(response.document_alert || "");

          // Loop through the approvers and update the elements accordingly
          if (response.approvers && response.approvers.length > 0) {
            response.approvers.forEach(function (approver) {
              if (approver.approver_role) {
                switch (approver.approver_role) {
                  case "QA ENGINEER":
                    $("#approvd_by_engineer").text(
                      approver.fname + " " + approver.lname
                    );
                    break;
                  case "QA MANAGER":
                    $("#approvd_by_supv_mgr").text(
                      approver.fname + " " + approver.lname
                    );
                    break;
                  case "QA SUPERVISOR":
                    $("#approvd_by_supv_mgr").text(
                      approver.fname + " " + approver.lname
                    );
                    break;
                  case "SHELDAHL REPRESENTATIVE":
                    $("#approvd_by_SheldahlRep").text(
                      approver.fname + " " + approver.lname
                    );
                    break;
                  // Add more cases for other roles as needed
                  default:
                    // Handle default case if needed (optional)
                    console.log("Unknown role:", approver.approver_role);
                    break;
                }
              }
            });
          }

          // Disable all form elements to prevent modification
          //$('.lock, .locked').prop('disabled', true);
          $("#dispoModal").modal("show");
        }
      },
      error: function (jqXHR, textStatus, errorThrown) {
        console.log("Error fetching disposition data:", {
          status: jqXHR.status,
          statusText: jqXHR.statusText,
          responseText: jqXHR.responseText,
          textStatus: textStatus,
          errorThrown: errorThrown,
        });

        alert(`Failed to fetch disposition data.`);
      },
    });
  });
});
