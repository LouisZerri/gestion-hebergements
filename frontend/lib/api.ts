import type { 
  Hotel, 
  HotelFormData, 
  HotelFilters, 
  ApiResponse, 
  PaginatedResponse 
} from '@/types/api';

const API_BASE_URL = process.env.NEXT_PUBLIC_API_URL || 'http://localhost:8000/api';

class ApiService {
  private async request<T>(
    endpoint: string,
    options: RequestInit = {}
  ): Promise<ApiResponse<T>> {
    const url = `${API_BASE_URL}${endpoint}`;
    
    try {
      const response = await fetch(url, {
        ...options,
        headers: {
          'Accept': 'application/json',
          ...options.headers,
        },
      });

      const data = await response.json();
      
      if (!response.ok) {
        // Formater le message d'erreur pour être plus lisible
        if (response.status === 404) {
          throw {
            message: 'Hôtel non trouvé',
            code: 404,
            ...data
          };
        }
        
        if (response.status === 422) {
          throw {
            message: 'Erreur de validation',
            code: 422,
            errors: data.errors || {},
            ...data
          };
        }
        
        throw {
          message: data.message || 'Une erreur est survenue',
          code: response.status,
          ...data
        };
      }

      return data;
    } catch (error: any) {
      // Si c'est déjà notre erreur formatée, on la relance
      if (error.code) {
        throw error;
      }
      
      // Sinon, c'est une erreur réseau
      throw {
        message: 'Erreur de connexion au serveur',
        code: 0,
      };
    }
  }

  // Récupérer la liste des hôtels avec filtres
  async getHotels(filters?: HotelFilters): Promise<ApiResponse<PaginatedResponse<Hotel>>> {
    const params = new URLSearchParams();
    
    if (filters) {
      Object.entries(filters).forEach(([key, value]) => {
        if (value !== undefined && value !== null && value !== '') {
          params.append(key, String(value));
        }
      });
    }

    const queryString = params.toString();
    const endpoint = `/hotels${queryString ? `?${queryString}` : ''}`;
    
    return this.request<PaginatedResponse<Hotel>>(endpoint);
  }

  // Rechercher des hôtels
  async searchHotels(query: string, page = 1): Promise<ApiResponse<PaginatedResponse<Hotel>>> {
    return this.request<PaginatedResponse<Hotel>>(`/hotels/search?q=${encodeURIComponent(query)}&page=${page}`);
  }

  // Récupérer un hôtel par ID
  async getHotel(id: number): Promise<ApiResponse<Hotel>> {
    return this.request<Hotel>(`/hotels/${id}`);
  }

  // Créer un hôtel
  async createHotel(data: HotelFormData): Promise<ApiResponse<Hotel>> {
    return this.request<Hotel>('/hotels', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify(data),
    });
  }

  // Mettre à jour un hôtel
  async updateHotel(id: number, data: HotelFormData): Promise<ApiResponse<Hotel>> {
    return this.request<Hotel>(`/hotels/${id}`, {
      method: 'PUT',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify(data),
    });
  }

  // Supprimer un hôtel
  async deleteHotel(id: number): Promise<ApiResponse<null>> {
    return this.request<null>(`/hotels/${id}`, {
      method: 'DELETE',
    });
  }

  // Upload de photos
  async uploadPictures(hotelId: number, files: File[]): Promise<ApiResponse<any>> {
    const formData = new FormData();
    
    files.forEach((file) => {
      formData.append('pictures[]', file);
    });

    const url = `${API_BASE_URL}/hotels/${hotelId}/pictures`;
    
    const response = await fetch(url, {
      method: 'POST',
      body: formData,
      headers: {
        'Accept': 'application/json',
      },
    });

    const data = await response.json();
    
    if (!response.ok) {
      throw data;
    }

    return data;
  }

  // Mettre à jour la position d'une photo
  async updatePicturePosition(
    hotelId: number,
    pictureId: number,
    position: number
  ): Promise<ApiResponse<any>> {
    return this.request(`/hotels/${hotelId}/pictures/${pictureId}`, {
      method: 'PATCH',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify({ position }),
    });
  }

  // Supprimer une photo
  async deletePicture(hotelId: number, pictureId: number): Promise<ApiResponse<null>> {
    return this.request<null>(`/hotels/${hotelId}/pictures/${pictureId}`, {
      method: 'DELETE',
    });
  }

  // Construire l'URL complète d'une image
  getImageUrl(filepath: string): string {
    const baseUrl = process.env.NEXT_PUBLIC_API_URL?.replace('/api', '') || 'http://localhost:8000';
    return `${baseUrl}/storage/${filepath}`;
  }
}

export const apiService = new ApiService();