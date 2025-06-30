document.addEventListener("DOMContentLoaded", function () {
    let currentDate = new Date();
    let positionFilter = document.getElementById("position-filter");

    // Define colors for positions
    const positionColors = {
        "Network Admin": "bg-red-500",
        "IT Security Admin": "bg-green-500",
        "Application Security Admin": "bg-blue-500",
        "Database Admin": "bg-yellow-500",
        "System Admin": "bg-purple-500",
    };

    function getInitials(name) {
        return name.split(" ").map(word => word[0]).join("").toUpperCase();
    }

    function renderCalendar() {
        let month = currentDate.getMonth();
        let year = currentDate.getFullYear();
        let firstDay = new Date(year, month, 1).getDay();
        let lastDate = new Date(year, month + 1, 0).getDate();

        document.getElementById("currentMonth").textContent = 
            new Intl.DateTimeFormat('en-US', { month: 'long', year: 'numeric' }).format(currentDate);

        let grid = document.getElementById("calendarGrid");
        grid.innerHTML = "";

        // Fill empty cells before the first day
        for (let i = 0; i < firstDay; i++) {
            let emptyCell = document.createElement("div");
            emptyCell.classList.add("p-2");
            grid.appendChild(emptyCell);
        }

        // Get selected position
        let selectedPosition = positionFilter.value;

        // Generate calendar days
        for (let day = 1; day <= lastDate; day++) {
            let cell = document.createElement("div");
            cell.classList.add(
                "p-4", 
                "border", 
                "border-gray-200", 
                "dark:border-gray-700", 
                "rounded-lg", 
                "bg-white", 
                "dark:bg-gray-800", 
                "shadow-sm", 
                "hover:shadow-md", 
                "transition-shadow", 
                "cursor-pointer", 
                "flex", 
                "flex-col", 
                "justify-between"
            );
            cell.innerHTML = `<span class="font-semibold text-gray-900 dark:text-gray-100 text-lg">${day}</span>`;

            let shiftContainer = document.createElement("div");
            shiftContainer.classList.add("mt-2", "space-y-1");

            let shiftDetails = []; // Store shift details for modal
            let shiftCount = 0; // Track displayed shifts
            let maxShiftsToShow = 3; // Limit displayed shifts per day

            shifts.forEach(shift => {
                let shiftDate = new Date(shift.date);
                if (shiftDate.getFullYear() === year && shiftDate.getMonth() === month && shiftDate.getDate() === day) {
                    if (!selectedPosition || shift.user.position === selectedPosition) {
                        let initials = getInitials(shift.user.name);
                        let positionColor = positionColors[shift.user.position] || "bg-gray-400";

                        if (shiftCount < maxShiftsToShow) {
                            let shiftLabel = document.createElement("div");
                            shiftLabel.classList.add(
                                positionColor, 
                                "text-white", 
                                "rounded-full", 
                                "w-8", 
                                "h-8", 
                                "flex", 
                                "items-center", 
                                "justify-center", 
                                "text-sm", 
                                "font-bold", 
                                "shadow-md"
                            );
                            shiftLabel.textContent = initials;
                            shiftContainer.appendChild(shiftLabel);
                            shiftCount++;
                        }

                        // Store shift details for modal
                        shiftDetails.push(`${shift.user.name} - ${shift.shift_time}`);
                    }
                }
            });

            if (shiftCount >= maxShiftsToShow) {
                let moreLabel = document.createElement("div");
                moreLabel.classList.add("text-gray-500", "dark:text-gray-400", "text-xs", "mt-1");
                moreLabel.textContent = `+${shiftDetails.length - maxShiftsToShow} more`;
                shiftContainer.appendChild(moreLabel);
            }

            cell.appendChild(shiftContainer);
            grid.appendChild(cell);

            // Click event for opening modal
            cell.addEventListener("click", function () {
                openModal(day, month, year, shiftDetails);
            });
        }
    }
    

    // Function to change month
    window.changeMonth = function (direction) {
        currentDate.setMonth(currentDate.getMonth() + direction);
        renderCalendar();
    };

    // Filter by position
    positionFilter.addEventListener("change", renderCalendar);

    renderCalendar();
});

// Function to open modal
function openModal(day, month, year, shiftDetails) {
    let modal = document.getElementById("shiftModal");
    let modalDate = document.getElementById("modalDate");
    let modalShiftDetails = document.getElementById("modalShiftDetails");

    modalDate.textContent = `Schedule for ${day} ${new Intl.DateTimeFormat('en-US', { month: 'long' }).format(new Date(year, month, day))} ${year}`;
    modalShiftDetails.innerHTML = shiftDetails.length > 0 
        ? shiftDetails.map(detail => `<p class="text-gray-700 dark:text-gray-300">${detail}</p>`).join("") 
        : "<p class='text-gray-500 dark:text-gray-400'>No shifts available</p>";

    modal.classList.remove("hidden");
}

// Function to close modal
function closeModal() {
    document.getElementById("shiftModal").classList.add("hidden");
}

// Function to open modal with better UI
    function openModal(day, month, year, shiftDetails) {
        let modal = document.getElementById("shiftModal");
        let modalDate = document.getElementById("modalDate");
        let modalShiftDetails = document.getElementById("modalShiftDetails");

        modalDate.textContent = `Schedule for ${day} ${new Intl.DateTimeFormat('en-US', { month: 'long' }).format(new Date(year, month, day))} ${year}`;

        if (shiftDetails.length > 0) {
            modalShiftDetails.innerHTML = shiftDetails.map(detail => {
                let parts = detail.split(" - ");
                let userName = parts[0]; // Extract name
                let shiftType = parts.length > 1 ? parts[1].trim().toLowerCase() : ""; // Extract shift type

                let shiftClass = "bg-green-200 text-green-800 dark:bg-green-700 dark:text-green-200"; // Default to green

                if (shiftType.includes("vacation-leave")) {
                    shiftClass = "bg-blue-200 text-blue-800 dark:bg-blue-700 dark:text-black-200"; // Vacation Leave → Blue
                } else if (shiftType.includes("sick-leave")) {
                    shiftClass = "bg-blue-200 text-blue-800 dark:bg-blue-700 dark:text-black-200"; // Sick Leave → Blue
                } else if (shiftType.includes("day-off")) {
                    shiftClass = "bg-red-500 text-white dark:bg-red-700 dark:text-red-200"; // Day Off → Red
                }

                return `
                <div class="flex items-center space-x-2">
                    <span class="text-gray-800 dark:text-gray-200 text-sm font-medium">${userName}</span>
                    <span class="inline-block px-3 py-1 rounded-full text-sm font-medium shadow ${shiftClass}">
                        ${shiftType}
                    </span>
                </div>
            `;
            }).join("");
        } else {
            modalShiftDetails.innerHTML = "<p class='text-gray-500 dark:text-gray-400 text-sm'>No shifts available</p>";
        }

        modal.classList.remove("hidden");
    }



// Function to close modal
function closeModal() {
    document.getElementById("shiftModal").classList.add("hidden");
}
