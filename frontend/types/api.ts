// Types pour l'API Laravel
export interface Hotel {
  id: number;
  name: string;
  address_1: string;
  address_2?: string | null;
  zip_code: string;
  city: string;
  country: string;
  longitude: number;
  latitude: number;
  description?: string | null;
  max_capacity: number;
  price_per_night: number;
  created_at: string;
  updated_at: string;
  pictures: HotelPicture[];
}

export interface HotelPicture {
  id: number;
  hotel_id: number;
  filepath: string;
  filesize: number;
  position: number;
  created_at: string;
  updated_at: string;
}

export interface ApiResponse<T> {
  success: boolean;
  code: number;
  message: string;
  data?: T;
  errors?: Record<string, string[]>;
}

export interface PaginatedResponse<T> {
  current_page: number;
  data: T[];
  first_page_url: string;
  from: number;
  last_page: number;
  last_page_url: string;
  links: PaginationLink[];
  next_page_url: string | null;
  path: string;
  per_page: number;
  prev_page_url: string | null;
  to: number;
  total: number;
}

export interface PaginationLink {
  url: string | null;
  label: string;
  active: boolean;
}

export interface HotelFormData {
  name: string;
  address_1: string;
  address_2?: string;
  zip_code: string;
  city: string;
  country: string;
  longitude: number;
  latitude: number;
  description?: string;
  max_capacity: number;
  price_per_night: number;
}

export interface HotelFilters {
  name?: string;
  city?: string;
  country?: string;
  min_price?: number;
  max_price?: number;
  min_capacity?: number;
  sort_by?: 'name' | 'city' | 'price_per_night' | 'max_capacity' | 'created_at';
  sort_order?: 'asc' | 'desc';
  page?: number;
  per_page?: number;
}