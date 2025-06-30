let showTeamLogs = false;

// ✅ Load logs immediately on page load
document.addEventListener("DOMContentLoaded", function () {
    fetchLogs();
});

// ✅ Fetch logs from the server
function fetchLogs() {
    $.ajax({
        url: eodIndexUrl,
        type: "GET",
        success: function (response) {
            updateLogs(response.logs); // ✅ Immediately update logs on page load
        },
        error: function () {
            Swal.fire("Error", "Failed to load logs.", "error");
        }
    });
}

// ✅ Open the EOD submission modal
function openSubmissionModal() {
    let modal = document.getElementById('submission-modal');
    modal.classList.remove('opacity-0', 'pointer-events-none'); // Show modal
    modal.children[0].classList.remove('scale-95'); // Smooth scaling
}

// ✅ Close the EOD submission modal
function closeSubmissionModal() {
    let modal = document.getElementById('submission-modal');
    modal.classList.add('opacity-0', 'pointer-events-none'); // Hide modal
    modal.children[0].classList.add('scale-95'); // Shrink back
}

// ✅ Save EOD data
function saveData() {
    let schedule = document.getElementById('schedule').value;
    let tasks = document.getElementById('tasks').value.trim();

    if (tasks === "") {
        Swal.fire("Error", "Tasks cannot be empty!", "error");
        return;
    }

    $.ajax({
        url: saveEodUrl,
        type: "POST",
        data: { _token: csrfToken, schedule: schedule, tasks: tasks },
        success: function () {
            Swal.fire({
                title: "Success!",
                text: "End of day report submitted successfully!",
                icon: "success",
                confirmButtonText: "OK"
            }).then(() => {
                closeSubmissionModal();
                fetchLogs(); // ✅ Reload logs after submission
            });
        },
        error: function (xhr) {
            let errorMsg = xhr.responseJSON?.message || "Something went wrong. Please try again.";
            Swal.fire("Error", errorMsg, "error");
        }
    });
}

// ✅ Toggle between My Logs & Team Logs
function toggleLogs() {
    showTeamLogs = !showTeamLogs;
    let button = document.getElementById('toggleLogs');
    let toggleText = document.getElementById('toggleText');
    let toggleIcon = document.getElementById('toggleIcon');

    // ✅ Add animation
    button.classList.add('scale-95');
    setTimeout(() => button.classList.remove('scale-95'), 150);

    // ✅ Change button text & icon
    toggleText.textContent = showTeamLogs ? "Show My Logs" : "Show Team Logs";
    toggleIcon.className = showTeamLogs ? "fa-solid fa-user" : "fa-solid fa-users";

    // ✅ Fetch correct logs
    $.ajax({
        url: eodIndexUrl,
        type: "GET",
        data: { team: showTeamLogs },
        success: function (response) {
            updateLogs(response.logs);
        },
        error: function () {
            Swal.fire("Error", "Failed to load logs.", "error");
        }
    });
}

// ✅ Update log list dynamically
function updateLogs(logs) {
    const logsList = document.getElementById('logs-list');
    logsList.innerHTML = "";

    const today = new Date().toISOString().split("T")[0];

    logs.forEach(log => {
        const logDate = new Date(log.created_at).toISOString().split("T")[0];
        const formattedDate = new Date(log.created_at).toLocaleDateString('en-US', {
            month: 'long',
            day: '2-digit',
            year: 'numeric'
        });

        const li = document.createElement('li');
        li.className = "p-4 flex justify-between items-center bg-white dark:bg-gray-800 rounded-lg shadow-md hover:shadow-lg transition mb-2 dark:hover:bg-gray-700";
        li.setAttribute("data-user", log.user.name.toLowerCase());
        li.setAttribute("data-date", logDate);
        li.setAttribute("onclick", `viewLog('${log.user.name}', '${formattedDate}', \`${log.tasks}\`)`);

        // Inner content container
        let content = `
            <div class="text-gray-800 dark:text-gray-200">
                <p class="font-semibold">${log.user.name}</p>
                <p class="text-sm text-gray-500 dark:text-gray-400">${formattedDate}</p>
            </div>
        `;

        // Only show edit button if it's the current user's log and it's from today
        if (log.user_id === loggedInUserId && logDate === today) {
            content += `
                <button onclick="openEditModal(event, ${log.id}, \`${log.tasks}\`)"
                    class="ml-4 p-2 text-white bg-red-500 rounded-lg hover:bg-red-600 transition flex items-center justify-center">
                    <i class="fa-solid fa-pen-to-square text-lg"></i>
                </button>
            `;
        }

        li.innerHTML = content;
        logsList.appendChild(li);
    });
}


// ✅ Open Log Modal and Insert Data
function viewLog(user, date, tasks) {
    // Replace newlines with <br> for proper formatting
    let formattedTasks = tasks.replace(/\n/g, "<br>");

    document.getElementById("logUser").innerText = user;
    document.getElementById("logDate").innerText = date;
    document.getElementById("logTasks").innerHTML = formattedTasks; // ✅ Ensure line breaks work

    document.getElementById("logDetailsModal").classList.remove("hidden"); // ✅ Show modal
}

// ✅ Close Modal
function closeLogModal() {
    document.getElementById("logDetailsModal").classList.add("hidden"); // ✅ Hide modal
}


// ✅ Search & Date Filter Function
function filterLogs() {
    let searchQuery = document.getElementById('search').value.toLowerCase();
    let selectedDate = document.getElementById('filter-date').value;
    let logs = document.querySelectorAll('#logs-list li');

    logs.forEach(log => {
        let logDate = log.getAttribute('data-date');
        let userName = log.getAttribute('data-user');

        let matchesSearch = !searchQuery || userName.includes(searchQuery);
        let matchesDate = !selectedDate || logDate === selectedDate;

        log.style.display = matchesSearch && matchesDate ? "flex" : "none";
    });
}

// ✅ Attach event listeners
document.getElementById('search').addEventListener('keyup', filterLogs);
document.getElementById('filter-date').addEventListener('change', filterLogs);

// ✅ Reset Date Filter
function clearDateFilter() {
    document.getElementById('filter-date').value = "";
    filterLogs();
}

let editingLogId = null;

// ✅ Open Edit Modal
function openEditModal(event, logId, tasks) {
    event.stopPropagation(); // Prevents log click event from triggering

    editingLogId = logId;
    document.getElementById('edit-tasks').value = tasks;

    let modal = document.getElementById('edit-modal');
    modal.classList.remove('opacity-0', 'pointer-events-none');
    modal.children[0].classList.remove('scale-95');

    let editButton = event.currentTarget;
    editButton.classList.add('rotate-12');
    setTimeout(() => editButton.classList.remove('rotate-12'), 200);
}

// ✅ Close Edit Modal
function closeEditModal() {
    let modal = document.getElementById('edit-modal');
    modal.classList.add('opacity-0', 'pointer-events-none');
    modal.children[0].classList.add('scale-95');
}

// ✅ Update Log
function updateLog() {
    let updatedTasks = document.getElementById('edit-tasks').value.trim();

    if (updatedTasks === "") {
        Swal.fire("Error", "Tasks cannot be empty!", "error");
        return;
    }

    $.ajax({
        url: `/eod/update/${editingLogId}`,
        type: "PUT",
        data: { _token: csrfToken, tasks: updatedTasks },
        success: function () {
            Swal.fire("Success!", "Log updated successfully!", "success").then(() => {
                closeEditModal();
                fetchLogs();
            });
        },
        error: function () {
            Swal.fire("Error", "Something went wrong. Please try again.", "error");
        }
    });
}


