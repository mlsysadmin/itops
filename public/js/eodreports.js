document.addEventListener('DOMContentLoaded', function () {
    // Weekly Filter Elements
    const weeklyDropdown = document.getElementById('weekly-filter');
    const weeklyContainer = document.getElementById('weekly-missing-users');

    // Monthly Filter Elements
    const monthlyDropdown = document.getElementById('monthly-filter');
    const monthlyContainer = document.getElementById('monthly-missing-users');
    const currentYear = new Date().getFullYear();
    monthlyDropdown.setAttribute('data-current-year', currentYear);

    // Yearly Filter Elements
    const yearlyDropdown = document.getElementById('yearly-filter');
    const yearlyContainer = document.getElementById('yearly-missing-users');
    yearlyDropdown.setAttribute('data-current-year', currentYear); // Set the current year in the dropdown

    const dateRangeContainer = document.getElementById('date-range-results');
    const startDateInput = document.getElementById('start-date');
    const endDateInput = document.getElementById('end-date');

    // Function to fetch logs based on date range
    function fetchDateRangeLogs(startDate, endDate) {
        // Convert dates to correct format (YYYY-MM-DD)
        const formattedStartDate = startDate.toISOString().split('T')[0];
        const formattedEndDate = endDate.toISOString().split('T')[0];

        fetch(`/get-date-range-logs?start_date=${encodeURIComponent(formattedStartDate)}&end_date=${encodeURIComponent(formattedEndDate)}`)
            .then(response => response.json())
            .then(data => {
                dateRangeContainer.innerHTML = '';

                if (data.length === 0) {
                    dateRangeContainer.innerHTML = '<p class="text-gray-500 dark:text-gray-400 italic">No users missing logs for the selected date range.</p>';
                    return;
                }

                data.forEach(user => {
                    const userCard = document.createElement('div');
                    userCard.className = "flex items-center justify-between bg-white dark:bg-gray-800 p-2 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 mb-2";

                    const userInfo = document.createElement('div');
                    userInfo.className = "flex items-center gap-3";

                    const avatar = document.createElement('img');
                    avatar.src = user.avatar || '/images/default-avatar.png';
                    avatar.alt = user.name;
                    avatar.className = "w-8 h-8 rounded-full object-cover border dark:border-gray-600";

                    const name = document.createElement('span');
                    name.textContent = user.name;
                    name.className = "text-sm font-medium text-gray-800 dark:text-gray-200";

                    const missedCount = document.createElement('span');
                    missedCount.className = "text-xs text-gray-600 dark:text-gray-400";
                    missedCount.textContent = `Missed logs: ${user.missed_logs_count}`;

                    userInfo.appendChild(avatar);
                    userInfo.appendChild(name);
                    userCard.appendChild(userInfo);
                    userCard.appendChild(missedCount);
                    dateRangeContainer.appendChild(userCard);
                });
            })
            .catch(err => {
                dateRangeContainer.innerHTML = '<p class="text-red-500 dark:text-red-400">Failed to load data.</p>';
                console.error(err);
            });
    }

    // Function to fetch yearly logs
    function fetchYearlyLogs(year) {
        fetch(`/get-yearly-logs?year=${encodeURIComponent(year)}`)
            .then(response => response.json())
            .then(data => {
                yearlyContainer.innerHTML = '';

                if (data.length === 0) {
                    yearlyContainer.innerHTML = '<p class="text-gray-500 dark:text-gray-400 italic">No users missing logs for this year.</p>';
                    return;
                }

                data.forEach(user => {
                    const userCard = document.createElement('div');
                    userCard.className = "flex items-center justify-between bg-white dark:bg-gray-800 p-2 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 mb-2";

                    const userInfo = document.createElement('div');
                    userInfo.className = "flex items-center gap-3";

                    const avatar = document.createElement('img');
                    avatar.src = user.avatar || '/images/default-avatar.png';
                    avatar.alt = user.name;
                    avatar.className = "w-8 h-8 rounded-full object-cover border dark:border-gray-600";

                    const name = document.createElement('span');
                    name.textContent = user.name;
                    name.className = "text-sm font-medium text-gray-800 dark:text-gray-200";

                    const missedCount = document.createElement('span');
                    missedCount.className = "text-xs text-gray-600 dark:text-gray-400";
                    missedCount.textContent = `Missed logs: ${user.missed_logs_count}`;

                    userInfo.appendChild(avatar);
                    userInfo.appendChild(name);
                    userCard.appendChild(userInfo);
                    userCard.appendChild(missedCount);
                    yearlyContainer.appendChild(userCard);
                });
            })
            .catch(err => {
                yearlyContainer.innerHTML = '<p class="text-red-500 dark:text-red-400">Failed to load data.</p>';
                console.error(err);
            });
    }

    // Function to fetch weekly logs
    function fetchWeeklyLogs(week) {
        fetch(`/get-weekly-logs?week=${encodeURIComponent(week)}`)
            .then(response => response.json())
            .then(data => {
                weeklyContainer.innerHTML = '';

                if (data.length === 0) {
                    weeklyContainer.innerHTML = '<p class="text-gray-500 dark:text-gray-400 italic">No users missing logs this week.</p>';
                    return;
                }

                data.forEach(user => {
                    const userCard = document.createElement('div');
                    userCard.className = "flex items-center justify-between bg-white dark:bg-gray-800 p-2 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 mb-2";

                    const userInfo = document.createElement('div');
                    userInfo.className = "flex items-center gap-3";

                    const avatar = document.createElement('img');
                    avatar.src = user.avatar || '/images/default-avatar.png';
                    avatar.alt = user.name;
                    avatar.className = "w-8 h-8 rounded-full object-cover border dark:border-gray-600";

                    const name = document.createElement('span');
                    name.textContent = user.name;
                    name.className = "text-sm font-medium text-gray-800 dark:text-gray-200";

                    const missedCount = document.createElement('span');
                    missedCount.className = "text-xs text-gray-600 dark:text-gray-400";
                    missedCount.textContent = `Missed logs: ${user.missed_logs_count}`;

                    userInfo.appendChild(avatar);
                    userInfo.appendChild(name);
                    userCard.appendChild(userInfo);
                    userCard.appendChild(missedCount);
                    weeklyContainer.appendChild(userCard);
                });
            })
            .catch(err => {
                weeklyContainer.innerHTML = '<p class="text-red-500 dark:text-red-400">Failed to load data.</p>';
                console.error(err);
            });
    }

    // Function to fetch monthly logs
    function fetchMonthlyLogs(month) {
        const selectedMonth = parseInt(month);
        const currentYear = parseInt(monthlyDropdown.getAttribute('data-current-year'));

        if (selectedMonth && selectedMonth >= 1 && selectedMonth <= 12) {
            fetch(`/get-monthly-logs?month=${encodeURIComponent(selectedMonth)}&year=${encodeURIComponent(currentYear)}`)
                .then(response => response.json())
                .then(data => {
                    monthlyContainer.innerHTML = '';

                    if (data.length === 0) {
                        monthlyContainer.innerHTML = '<p class="text-gray-500 dark:text-gray-400 italic">No users missing logs for this month.</p>';
                        return;
                    }

                    data.forEach(user => {
                        const userCard = document.createElement('div');
                        userCard.className = "flex items-center gap-3 bg-white dark:bg-gray-800 p-2 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 mb-2";

                        const avatar = document.createElement('img');
                        avatar.src = user.avatar || '/images/default-avatar.png';
                        avatar.alt = user.name;
                        avatar.className = "w-8 h-8 rounded-full object-cover border dark:border-gray-600";

                        const name = document.createElement('span');
                        name.textContent = user.name;
                        name.className = "text-sm font-medium text-gray-800 dark:text-gray-200";

                        const missedCount = document.createElement('span');
                        missedCount.className = "text-xs text-gray-600 dark:text-gray-400";
                        missedCount.textContent = `Missed logs: ${user.missed_logs_count}`;

                        userCard.appendChild(avatar);
                        userCard.appendChild(name);
                        userCard.appendChild(missedCount);
                        monthlyContainer.appendChild(userCard);
                    });
                })
                .catch(err => {
                    monthlyContainer.innerHTML = '<p class="text-red-500 dark:text-red-400">Failed to load data.</p>';
                    console.error(err);
                });
        } else {
            monthlyContainer.innerHTML = '<p class="text-gray-500 dark:text-gray-400 italic">Please select a valid month from the current year.</p>';
        }
    }

    // Initial load for Weekly Logs
    fetchWeeklyLogs('This Week');

    weeklyDropdown.addEventListener('change', function () {
        fetchWeeklyLogs(this.value);
    });

    // Initial load for Monthly Logs
    const currentMonth = new Date().getMonth() + 1;
    monthlyDropdown.value = currentMonth;
    fetchMonthlyLogs(currentMonth);

    monthlyDropdown.addEventListener('change', function () {
        fetchMonthlyLogs(this.value);
    });

    // Initial load for Yearly Logs
    fetchYearlyLogs(currentYear);

    yearlyDropdown.addEventListener('change', function () {
        fetchYearlyLogs(this.value);
    });

     // Add event listener to the date inputs to trigger fetch when dates are selected
     startDateInput.addEventListener('change', function () {
        const startDate = new Date(startDateInput.value);
        const endDate = new Date(endDateInput.value);

        if (startDate && endDate && startDate <= endDate) {
            fetchDateRangeLogs(startDate, endDate);
        }
    });

    endDateInput.addEventListener('change', function () {
        const startDate = new Date(startDateInput.value);
        const endDate = new Date(endDateInput.value);

        if (startDate && endDate && startDate <= endDate) {
            fetchDateRangeLogs(startDate, endDate);
        }
    });

});
