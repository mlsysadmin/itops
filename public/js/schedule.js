document.addEventListener("DOMContentLoaded", function () {
    let currentDate = dayjs();
    let assignedShifts = {};
    fetchShifts();

    function fetchShifts() {
        const month = currentDate.format("M");
        const year = currentDate.format("YYYY");

        fetch(`/shifts?month=${month}&year=${year}`)
            .then((response) => response.json())
            .then((data) => {
                assignedShifts = {};

                data.forEach((shift) => {
                    if (!shift.user) return;
                    const dateKey = shift.date;
                    if (!assignedShifts[dateKey]) assignedShifts[dateKey] = [];
                    assignedShifts[dateKey].push({
                        name: shift.user.name,
                        shift: shift.shift_time,
                    });
                });

                renderCalendar();
            })
            .catch((error) => console.error("Error fetching shifts:", error));
    }

    function renderCalendar() {
        const monthStart = currentDate.startOf("month");
        const firstDayIndex = monthStart.day();
        const lastDate = currentDate.endOf("month").date();

        document.getElementById("currentMonth").innerText =
            currentDate.format("MMMM YYYY");
        let daysHtml = "";

        for (let x = firstDayIndex; x > 0; x--) {
            daysHtml += `<div class='text-gray-400 p-4 bg-gray-100 rounded-lg'></div>`;
        }

        for (let i = 1; i <= lastDate; i++) {
            const dateKey = currentDate.date(i).format("YYYY-MM-DD");
            const shifts = assignedShifts[dateKey] || [];

            const shiftsHtml = shifts
                .slice(0, 3)
                .map((emp) => {
                    let initials = emp.name
                        .split(" ")
                        .map((n) => n[0])
                        .join("")
                        .toUpperCase();
                    let shiftColor = emp.shift.includes("leave")
                        ? "bg-blue-700"
                        : emp.shift.includes("day-off")
                        ? "bg-red-500"
                        : "bg-green-500";

                    return `<div class="shift-box text-white flex items-center justify-center ${shiftColor}" title="${emp.name} - ${emp.shift}" style="width: 24px; height: 24px; font-size: 12px; font-weight: bold; border-radius: 4px;">${initials}</div>`;
                })
                .join(" ");

            const moreShiftsHtml =
                shifts.length > 3
                    ? `<span class="text-xs text-gray-500">+${
                          shifts.length - 3
                      } more</span>`
                    : "";

            daysHtml += `
                <div class='p-4 border rounded-lg cursor-pointer hover:bg-red-200 min-h-[80px] flex flex-col items-center justify-center' data-date='${dateKey}' onclick='openDateDetails("${dateKey}")'>
                    <span class='font-semibold text-lg'>${i}</span>
                    <div class='assigned-employee mt-1 flex flex-wrap justify-center gap-1 max-w-full'>
                        ${shiftsHtml} ${moreShiftsHtml}
                    </div>
                </div>`;
        }

        document.getElementById("calendarGrid").innerHTML = daysHtml;
    }

    function changeMonth(step) {
        currentDate = currentDate.add(step, "month");
        fetchShifts();
    }

    function openDateDetails(dateKey) {
        let detailsHtml =
            assignedShifts[dateKey] && assignedShifts[dateKey].length > 0
                ? assignedShifts[dateKey]
                      .map((emp, index) => {
                          // Assign colors based on shift type
                          let shiftColor = "bg-green-500 text-white"; // Default: Green
                          if (emp.shift.includes("leave"))
                              shiftColor = "bg-blue-500 text-white";
                          if (emp.shift.includes("day-off"))
                              shiftColor = "bg-red-500 text-white";

                          return `
                    <div class="bg-gray-50 dark:bg-gray-800 p-4 rounded-lg shadow flex justify-between items-center border dark:border-gray-700">
                        <!-- Employee Name & Shift Pill -->
                        <div class="flex items-center space-x-3">
                            <span class="text-gray-900 dark:text-gray-100 font-medium">${
                                emp.name
                            }</span>
                            <span class="px-4 py-1 rounded-full ${shiftColor} text-sm font-semibold">
                                ${emp.shift.replace("-", " ")}
                            </span>
                        </div>

                        <!-- Edit Button -->
                        <button onclick="openEditShiftModal('${dateKey}', ${index})"
                            class="text-blue-500 dark:text-blue-300 hover:text-blue-700 dark:hover:text-blue-500 p-2 rounded-lg transition">
                            <i class="fa-solid fa-pen"></i>
                        </button>
                    </div>
                `;
                      })
                      .join("")
                : "<p class='text-gray-500 dark:text-gray-400 text-center'>No shifts assigned for this date.</p>";

        document.getElementById("dateDetailsContent").innerHTML = detailsHtml;
        document.getElementById("dateDetailsModal").classList.remove("hidden");
    }

    function closeDateDetails() {
        document.getElementById("dateDetailsModal").classList.add("hidden");
    }

    function openShiftAssignmentModal() {
        document
            .getElementById("shiftAssignmentModal")
            .classList.remove("hidden");
    }

    function closeShiftAssignmentModal() {
        document.getElementById("shiftAssignmentModal").classList.add("hidden");
    }

    function assignShifts() {
        let selectedEmployeeIds = Array.from(
            document.querySelectorAll(".employee-checkbox:checked")
        ).map((checkbox) => checkbox.value);
        let skipSundays = document.getElementById("skipSundays").checked;
        let startDate = document.getElementById("startDate").value;
        let endDate = document.getElementById("endDate").value;
        let shift = document.getElementById("shiftSelect").value;

        if (
            selectedEmployeeIds.length === 0 ||
            !startDate ||
            !endDate ||
            !shift
        ) {
            Swal.fire(
                "Error",
                "Please select at least one employee, a valid date range, and a shift.",
                "error"
            );
            return;
        }

        let dateList = [];
        let current = dayjs(startDate);
        let lastDate = dayjs(endDate);

        while (current.isBefore(lastDate) || current.isSame(lastDate, "day")) {
            if (!(skipSundays && current.day() === 0)) {
                // 0 = Sunday
                dateList.push(current.format("YYYY-MM-DD"));
            }
            current = current.add(1, "day");
        }

        fetch("/shifts", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": document.querySelector(
                    'meta[name="csrf-token"]'
                ).content,
            },
            body: JSON.stringify({
                user_ids: selectedEmployeeIds,
                dates: dateList,
                shift_time: shift,
            }),
        })
            .then((response) => response.json())
            .then(() => {
                fetchShifts();
                closeShiftAssignmentModal();
                Swal.fire(
                    "Success",
                    "Shifts assigned successfully.",
                    "success"
                );
            })
            .catch((error) =>
                Swal.fire(
                    "Error",
                    "Shift assignment failed. Please try again.",
                    "error"
                )
            );
    }

    function openEditShiftModal(dateKey, shiftIndex) {
        document.getElementById("editDateKey").value = dateKey;
        document.getElementById("editShiftIndex").value = shiftIndex;
        document.getElementById("editShiftModal").classList.remove("hidden");
    }

    function closeEditShiftModal() {
        document.getElementById("editShiftModal").classList.add("hidden");
    }

    function saveEditedShift() {
        let dateKey = document.getElementById("editDateKey").value;
        let shiftIndex = document.getElementById("editShiftIndex").value;
        let newShift = document.getElementById("editShiftSelect").value;

        if (!dateKey || shiftIndex === "" || !newShift) {
            Swal.fire("Error", "Please select a valid shift.", "error");
            return;
        }

        let employeeName = assignedShifts[dateKey][shiftIndex].name;

        fetch("/shifts/update", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": document.querySelector(
                    'meta[name="csrf-token"]'
                ).content,
            },
            body: JSON.stringify({
                date: dateKey,
                name: employeeName,
                shift_time: newShift,
            }),
        })
            .then(() => {
                fetchShifts();
                closeEditShiftModal();
                Swal.fire("Success", "Shift updated successfully.", "success");
            })
            .catch((error) =>
                Swal.fire(
                    "Error",
                    "Shift update failed. Please try again.",
                    "error"
                )
            );
    }

    window.openDateDetails = openDateDetails;
    window.closeDateDetails = closeDateDetails;
    window.changeMonth = changeMonth;
    window.openShiftAssignmentModal = openShiftAssignmentModal;
    window.closeShiftAssignmentModal = closeShiftAssignmentModal;
    window.assignShifts = assignShifts;
    window.openEditShiftModal = openEditShiftModal;
    window.closeEditShiftModal = closeEditShiftModal;
    window.saveEditedShift = saveEditedShift;
});
