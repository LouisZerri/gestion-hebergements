import toast from 'react-hot-toast';

export const showToast = {
  success: (message: string) => {
    toast.success(message, {
      duration: 4000,
      position: 'top-right',
      style: {
        background: '#48BB78',
        color: '#fff',
        padding: '16px',
        borderRadius: '8px',
      },
    });
  },
  
  error: (message: string) => {
    toast.error(message, {
      duration: 5000,
      position: 'top-right',
      style: {
        background: '#F56565',
        color: '#fff',
        padding: '16px',
        borderRadius: '8px',
      },
    });
  },
  
  loading: (message: string) => {
    return toast.loading(message, {
      position: 'top-right',
    });
  },
  
  dismiss: (toastId: string) => {
    toast.dismiss(toastId);
  },
};