// Mobile sidebar toggle
document.addEventListener("DOMContentLoaded", () => {
  const sidebarToggle = document.querySelector(".navbar-toggler")
  const sidebar = document.querySelector("#sidebarMenu")
  const bootstrap = window.bootstrap // Declare the bootstrap variable

  if (sidebarToggle && sidebar) {
    sidebarToggle.addEventListener("click", () => {
      sidebar.classList.toggle("show-sidebar")
    })

    // Close sidebar when clicking outside on mobile
    document.addEventListener("click", (e) => {
      if (window.innerWidth < 768 && !sidebar.contains(e.target) && !sidebarToggle.contains(e.target)) {
        sidebar.classList.remove("show-sidebar")
      }
    })
  }

  // Initialize tooltips
  const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-toggle="tooltip"]'))
  tooltipTriggerList.map((tooltipTriggerEl) => new bootstrap.Tooltip(tooltipTriggerEl))

  // Initialize popovers
  const popoverTriggerList = [].slice.call(document.querySelectorAll('[data-toggle="popover"]'))
  popoverTriggerList.map((popoverTriggerEl) => new bootstrap.Popover(popoverTriggerEl))

  // Add active class to current nav item
  const currentLocation = window.location.pathname
  const navLinks = document.querySelectorAll(".sidebar .nav-link")

  navLinks.forEach((link) => {
    const href = link.getAttribute("href")
    if (href && currentLocation.includes(href) && href !== "/") {
      link.classList.add("active")
    } else if (href === "/" && currentLocation === "/") {
      link.classList.add("active")
    }
  })

  // Confirm delete actions
  const deleteButtons = document.querySelectorAll(".btn-delete")
  deleteButtons.forEach((button) => {
    button.addEventListener("click", (e) => {
      if (!confirm("Are you sure you want to delete this item? This action cannot be undone.")) {
        e.preventDefault()
      }
    })
  })

  // File input preview
  const fileInputs = document.querySelectorAll(".custom-file-input")
  fileInputs.forEach((input) => {
    input.addEventListener("change", function () {
      const fileName = this.files[0].name
      const label = this.nextElementSibling
      label.textContent = fileName
    })
  })

  // Notification badge update
  const notificationCount = document.querySelector(".notification-count")
  if (notificationCount) {
    // This would typically be updated via AJAX in a real application
    const count = Number.parseInt(notificationCount.textContent)
    if (count > 0) {
      notificationCount.style.display = "inline-block"
    } else {
      notificationCount.style.display = "none"
    }
  }

  // Auto-hide success alerts after 5 seconds
  setTimeout(() => {
    const alerts = document.querySelectorAll(".alert.alert-dismissible.alert-success, .alert.auto-dismiss")
    alerts.forEach((alert) => {
      if (bootstrap && bootstrap.Alert) {
        const bsAlert = bootstrap.Alert.getInstance(alert) || new bootstrap.Alert(alert)
        bsAlert.close()
      }
    })
  }, 5000)
})
