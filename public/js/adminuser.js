document.addEventListener("DOMContentLoaded", () => {
    const modal = document.getElementById("userModal");
    const modalClose = document.getElementById("modalClose");
    const saveButton = document.getElementById("saveUser");
    const deleteButton = document.getElementById("deleteUser");

    // Add User Modal Elements
    const addUserModal = document.getElementById("addUserModal");
    const openAddUserModalButton = document.getElementById("openAddUserModal");
    const closeAddUserModalButton = document.getElementById("closeAddUserModal");
    const addUsersForm = document.getElementById("addUsersForm");

    let selectedUser = null;

    // Open Edit User Modal
    window.openModal = (userData) => {
        selectedUser = userData;

        document.getElementById("modalAvatar").src = userData.avatar;
        document.getElementById("modalName").textContent = userData.name;
        document.getElementById("modalEmail").textContent = userData.email;
        document.getElementById("modalPosition").value = userData.position;
        document.getElementById("modalRole").value = userData.role;

        modal.classList.remove("hidden");
    };

    // Close Edit User Modal
    modalClose?.addEventListener("click", () => modal.classList.add("hidden"));

    // Open Add User Modal
    openAddUserModalButton?.addEventListener("click", () => {
        addUserModal.classList.remove("hidden");
    });

    // Close Add User Modal
    closeAddUserModalButton?.addEventListener("click", () => {
        addUserModal.classList.add("hidden");
    });

    // Handle "Add Users" Form Submission
    addUsersForm?.addEventListener("submit", async (event) => {
        event.preventDefault();

        let emails = document.getElementById("emails").value.split(",").map(email => email.trim());
        let role = document.getElementById("role").value;
        let position = document.getElementById("position").value;

        if (emails.length === 0 || emails[0] === "" || !role || !position) {
            Swal.fire("Error!", "Please fill in all required fields.", "error");
            return;
        }

        Swal.fire({
            title: "Confirm Addition",
            text: `Are you sure you want to add ${emails.length} user(s) with the role "${role}"?`,
            icon: "question",
            showCancelButton: true,
            confirmButtonText: "Yes, add them!",
            cancelButtonText: "Cancel",
        }).then(async (result) => {
            if (result.isConfirmed) {
                const response = await fetch("/admin/users/create", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content")
                    },
                    body: JSON.stringify({
                        emails: emails,
                        role: role,
                        position: position
                    })
                });

                const result = await response.json();

                if (response.ok) {
                    Swal.fire("Success!", result.message, "success").then(() => {
                        document.getElementById("emails").value = "";
                        document.getElementById("role").value = "";
                        document.getElementById("position").value = "";
                        addUserModal.classList.add("hidden"); // Close modal
                        location.reload(); // Refresh page
                    });
                } else {
                    Swal.fire("Error!", result.message || "Something went wrong.", "error");
                }
            }
        });
    });

    // Save User with SweetAlert2
    saveButton?.addEventListener("click", async () => {
        if (!selectedUser) return;

        Swal.fire({
            title: "Confirm Update",
            text: "Are you sure you want to update this user?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Yes, update it!",
            cancelButtonText: "No, cancel",
        }).then(async (result) => {
            if (result.isConfirmed) {
                const response = await fetch(`/admin/users/update/${selectedUser.id}`, {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        role: document.getElementById("modalRole").value,
                        position: document.getElementById("modalPosition").value
                    })
                });

                const result = await response.json();
                Swal.fire("Updated!", result.message, "success").then(() => location.reload());
            }
        });
    });

    // Delete User with SweetAlert2
    deleteButton?.addEventListener("click", async () => {
        if (!selectedUser) return;

        Swal.fire({
            title: "Are you sure?",
            text: "This action cannot be undone!",
            icon: "error",
            showCancelButton: true,
            confirmButtonText: "Yes, delete it!",
            cancelButtonText: "No, keep it",
        }).then(async (result) => {
            if (result.isConfirmed) {
                const response = await fetch(`/admin/users/delete/${selectedUser.id}`, {
                    method: "DELETE",
                    headers: { "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content }
                });

                const result = await response.json();
                Swal.fire("Deleted!", result.message, "success").then(() => location.reload());
            }
        });
    });
});
