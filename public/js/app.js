document.addEventListener('DOMContentLoaded', function() {
  if (typeof flatpickr !== 'undefined') {
    flatpickr('.datepicker', {
      locale: 'pt',
      dateFormat: 'd/m/Y',
      allowInput: true,
      disableMobile: true,
      maxDate: 'today',
    });
  }

  initConfirmModal();
  initFormModals();

  const toggle = document.getElementById('sidebarToggle');
  const sidebar = document.getElementById('sidebar');

  if (toggle && sidebar) {
    toggle.addEventListener('click', function() {
      sidebar.classList.toggle('open');
    });

    document.addEventListener('click', function(e) {
      if (window.innerWidth <= 768 && sidebar.classList.contains('open')) {
        if (!sidebar.contains(e.target) && !toggle.contains(e.target)) {
          sidebar.classList.remove('open');
        }
      }
    });
  }
});

function initConfirmModal() {
  const modal = document.getElementById('appConfirmModal');
  if (!modal) return;

  const titleEl = document.getElementById('appModalTitle');
  const messageEl = document.getElementById('appModalMessage');
  const iconEl = document.getElementById('appModalIcon');
  const confirmBtn = document.getElementById('appModalConfirm');
  const cancelBtn = document.getElementById('appModalCancel');
  let onConfirm = null;

  function closeModal() {
    modal.hidden = true;
    modal.setAttribute('aria-hidden', 'true');
    onConfirm = null;
    confirmBtn.classList.remove('danger');
    confirmBtn.hidden = false;
    cancelBtn.textContent = 'Cancelar';
  }

  function openModal(options) {
    const blocked = options.blocked === true;
    const variant = blocked ? 'danger' : (options.variant || 'primary');

    titleEl.textContent = options.title || 'Confirmar';
    messageEl.textContent = options.message || 'Deseja continuar?';

    if (blocked) {
      confirmBtn.hidden = true;
      cancelBtn.textContent = 'Entendi';
    } else {
      confirmBtn.hidden = false;
      confirmBtn.textContent = options.confirmLabel || 'Confirmar';
      confirmBtn.classList.toggle('danger', variant === 'danger');
      cancelBtn.textContent = 'Cancelar';
    }

    const iconClass = variant === 'danger' ? 'bi-exclamation-triangle' : 'bi-question-circle';
    iconEl.className = 'app-modal-icon ' + (variant === 'danger' ? 'danger' : 'primary');
    iconEl.innerHTML = '<i class="bi ' + iconClass + '"></i>';

    onConfirm = blocked ? null : options.onConfirm;
    modal.hidden = false;
    modal.setAttribute('aria-hidden', 'false');
    (blocked ? cancelBtn : confirmBtn).focus();
  }

  modal.querySelectorAll('[data-modal-close]').forEach(el => {
    el.addEventListener('click', closeModal);
  });

  confirmBtn.addEventListener('click', function() {
    if (onConfirm) onConfirm();
    closeModal();
  });

  document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape' && !modal.hidden) closeModal();
  });

  document.querySelectorAll('[data-confirm]').forEach(function(form) {
    form.addEventListener('submit', function(e) {
      if (form.dataset.confirmed === 'true') {
        form.dataset.confirmed = 'false';
        return;
      }

      e.preventDefault();

      const blocked = form.dataset.confirmBlocked === 'true';
      const variant = form.dataset.confirmVariant || 'primary';

      openModal({
        title: form.dataset.confirmTitle || 'Confirmar',
        message: form.dataset.confirmMessage || 'Deseja continuar?',
        confirmLabel: form.dataset.confirmLabel || 'Confirmar',
        variant: variant,
        blocked: blocked,
        onConfirm: function() {
          form.dataset.confirmed = 'true';
          form.requestSubmit();
        },
      });
    });
  });
}

function initFormModals() {
  const modals = document.querySelectorAll('.app-modal[id]');

  modals.forEach(function(modal) {
    if (modal.id === 'appConfirmModal') return;

    const modalId = modal.id;

    function closeFormModal() {
      modal.hidden = true;
      modal.setAttribute('aria-hidden', 'true');
    }

    function openFormModal() {
      modal.hidden = false;
      modal.setAttribute('aria-hidden', 'false');
      const firstField = modal.querySelector('select, input, textarea');
      if (firstField) firstField.focus();
    }

    document.querySelectorAll('[data-form-modal-open="' + modalId + '"]').forEach(function(btn) {
      btn.addEventListener('click', openFormModal);
    });

    modal.querySelectorAll('[data-form-modal-close]').forEach(function(el) {
      el.addEventListener('click', closeFormModal);
    });

    document.addEventListener('keydown', function(e) {
      if (e.key === 'Escape' && !modal.hidden) closeFormModal();
    });
  });
}
