$(document).ready(function () {
    $('#logsTable').DataTable({
        paging: true,
        searching: true,
        info: false,
        lengthChange: false,
        pageLength: 13,
        language: {
            paginate: { previous: "←", next: "→" },
            search: "Search here:",
        }
    });

    // Handle row click to show log details
    $(document).on('click', '.log-row', function () {
        let logData = $(this).attr('data-log');

        if (!logData) {
            console.error("Error: Missing data-log attribute.");
            return;
        }

        try {
            let log = JSON.parse(logData.trim());
            showLog(log);
        } catch (error) {
            console.error("Error parsing log data:", error);
            console.error("Raw data:", logData);
        }
    });

    // Close modal event
    $('#closeModal').click(function () {
        hideModal();
    });

    // Close modal on clicking outside content
    $(document).on('click', '#logModal', function (event) {
        if ($(event.target).closest('.bg-white').length === 0) {
            hideModal();
        }
    });
});

// Function to show the modal properly centered
function showLog(log) {
    if (!log || !log.name) {
        console.error("Invalid log data:", log);
        return;
    }

    $('#logAvatar').attr('src', log.avatar && log.avatar.trim() !== "" ? log.avatar : "/images/itopslogo.png");
    $('#logName').text(log.name);
    $('#logEmail').text(log.email || "No email provided");
    $('#logTasks').html(log.tasks ? log.tasks.replace(/\n/g, "<br>") : "No tasks recorded.");

    // Show modal with fade-in effect
    $('#logModal').removeClass('opacity-0 pointer-events-none').addClass('opacity-100 scale-100');
}

// Function to hide the modal
function hideModal() {
    $('#logModal').removeClass('opacity-100 scale-100').addClass('opacity-0 pointer-events-none');
}


