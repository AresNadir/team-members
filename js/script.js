document.addEventListener('DOMContentLoaded', (event) => {
  document.querySelectorAll('.read-more-link').forEach(link => {
      link.addEventListener('click', function() {
          const modalId = this.getAttribute('data-modal-id');
          const modal = document.getElementById(modalId);
          modal.style.display = 'block';
          document.body.style.overflow = 'hidden';
      });
  });

  document.querySelectorAll('.modal').forEach(modal => {
      modal.addEventListener('click', function(event) {
          if (event.target.className === 'modal') {
              modal.style.display = 'none';
              document.body.style.overflow = 'auto';
          }
      });
  });

  document.querySelectorAll('.modal-close_btn').forEach(button => {
    button.addEventListener('click', function() {
        const modal = this.closest('.modal');
        if (modal) {
            modal.style.display = 'none';
            document.body.style.overflow = 'auto';
        }
    });
});

  document.addEventListener('keydown', function(event) {
      if (event.key === 'Escape') {
          document.querySelectorAll('.modal').forEach(modal => {
              modal.style.display = 'none';
              document.body.style.overflow = 'auto';
          });
      }
  });
});
