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
            text: "note: not yet Disposition.",
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
          $("#document_alert").text(response.document_alert || " ");
          $("#notes").text(response.impact_analysis || " ");
          $("#contact_person").text(response.contact_person || " ");
          $("#other_specify").text(response.other_specify || " ");
          $("#yield_off").text(response.yield_off || " ");
          $("#da_no").text(response.da_no || " ");
          $("#rework_da_no").text(response.rework_da_no || " ");
          $("#wis_no").text(response.wis_no || "");
          $("#scrap_amount").text(response.scrap_amount || " ");
          $("#shipment_date").text(response.shipment_date || " ");

          if (
            Array.isArray(response.intervention_checkboxes) &&
            response.intervention_checkboxes.length > 0
          ) {
            let intervention_cb = [
              "actions_taken",
              "process_dispo",
              "resumption_reason",
              "instructions_detail",
              "documents_revision",
            ]; // Add more names here if needed

            $('input[name="further_eval"]').prop(
              "checked",
              response.intervention_checkboxes.some(
                (cb) => cb.checkbox_name === "F1"
              )
            );
            intervention_cb.forEach(function (checkbox) {
              $('input[name="' + checkbox + '[]"]').each(function () {
                let checkboxValue = $(this).val();
                let isChecked = response.intervention_checkboxes.some(
                  (cb) => cb.checkbox_name === checkboxValue
                );
                $(this).prop("checked", isChecked);
              });
            });
          }

          if (
            Array.isArray(response.intervention_inputs) &&
            response.intervention_inputs.length > 0
          ) {
            let intervention_inp = [
              "affected_process",
              "other_resumption",
              "process_instruction",
              "document_alert_s",
              "other_specify_s",
              "released_by",
              "acknowledgment_signature",
              "head_signature",
              "prod_manager_signature",
            ];

            intervention_inp.forEach(function (input) {
              let found = response.intervention_inputs.find(
                (obj) => obj.input_name === input
              );
              if (found) {
                $("#" + input).text(found.inputted_data || "");
              }
            });
          }

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

          //field for file query
          const fileList = $("#fileList");
          fileList.empty(); // Clear old stuff

          if (
            Array.isArray(response.files_attach) &&
            response.files_attach.length > 0
          ) {
            response.files_attach.forEach((file) => {
              const fileBox = $("<div>").addClass("mb-3 p-2 border rounded");

              const button = $("<button>")
                .addClass("btn btn-primary btn-sm")
                .text(file.name)
                .on("click", function (e) {
                  e.preventDefault();

                  const fileUrl = file.path;

                  // Try to fetch headers and check size
                  fetch(fileUrl, { method: "HEAD" })
                    .then((res) => {
                      const size = parseInt(
                        res.headers.get("Content-Length"),
                        10
                      );

                      // If under 3MB, open in new tab
                      if (size && size < 3 * 1024 * 1024) {
                        window.open(fileUrl, "_blank");
                      } else {
                        // Otherwise, force download
                        const a = document.createElement("a");
                        a.href = fileUrl;
                        a.download = file.name;
                        document.body.appendChild(a);
                        a.click();
                        document.body.removeChild(a);
                      }
                    })
                    .catch((err) => {
                      alert("Failed to fetch file info. Opening normally...");
                      window.open(fileUrl, "_blank");
                    });
                });

              fileBox.append(button);
              fileList.append(fileBox);
            });
          } else {
            fileList.append(
              $("<p>").addClass("text-muted").text("No file attachments found.")
            );
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

  // Select the modal element
  let dispoModal = document.getElementById("dispoModal");

  // Listen for the modal close event
  dispoModal.addEventListener("hidden.bs.modal", function () {
    // Select all checkboxes and radio buttons inside the modal
    let inputs = dispoModal.querySelectorAll(
      "input[type='checkbox'], input[type='radio']"
    );

    // Loop through each input and uncheck it
    inputs.forEach((input) => {
      input.checked = false;
    });

    // Clear the text content of the specific <span> elements
    let clear_inp = [
      "affected_process",
      "other_resumption",
      "process_instruction",
      "document_alert_s",
      "other_specify_s",
      "released_by",
      "approvd_by_engineer",
      "approvd_by_supv_mgr",
      "approvd_by_SheldahlRep",
    ];

    clear_inp.forEach((id) => {
      let spanElement = dispoModal.querySelector(`#${id}`);
      if (spanElement) {
        spanElement.textContent = ""; // Clear the content of the <span>
      }
    });
  });
});
