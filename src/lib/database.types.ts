export type Json =
    | string
    | number
    | boolean
    | null
    | { [key: string]: Json | undefined }
    | Json[]

export interface Database {
    public: {
        Tables: {
            profiles: {
                Row: {
                    id: string
                    full_name: string | null
                    email: string | null
                    phone: string | null
                    location: string | null
                    photo_url: string | null
                    created_at: string
                    updated_at: string
                }
                Insert: {
                    id: string
                    full_name?: string | null
                    email?: string | null
                    phone?: string | null
                    location?: string | null
                    photo_url?: string | null
                    created_at?: string
                    updated_at?: string
                }
                Update: {
                    id?: string
                    full_name?: string | null
                    email?: string | null
                    phone?: string | null
                    location?: string | null
                    photo_url?: string | null
                    created_at?: string
                    updated_at?: string
                }
            }
            work_experience: {
                Row: {
                    id: string
                    profile_id: string | null
                    company_name: string
                    position: string
                    start_date: string
                    end_date: string | null
                    description: string | null
                    created_at: string
                    updated_at: string
                }
                Insert: {
                    id?: string
                    profile_id?: string | null
                    company_name: string
                    position: string
                    start_date: string
                    end_date?: string | null
                    description?: string | null
                    created_at?: string
                    updated_at?: string
                }
                Update: {
                    id?: string
                    profile_id?: string | null
                    company_name?: string
                    position?: string
                    start_date?: string
                    end_date?: string | null
                    description?: string | null
                    created_at?: string
                    updated_at?: string
                }
            }
            projects: {
                Row: {
                    id: string
                    profile_id: string | null
                    title: string
                    description: string | null
                    start_date: string | null
                    end_date: string | null
                    url: string | null
                    created_at: string
                    updated_at: string
                }
                Insert: {
                    id?: string
                    profile_id?: string | null
                    title: string
                    description?: string | null
                    start_date?: string | null
                    end_date?: string | null
                    url?: string | null
                    created_at?: string
                    updated_at?: string
                }
                Update: {
                    id?: string
                    profile_id?: string | null
                    title?: string
                    description?: string | null
                    start_date?: string | null
                    end_date?: string | null
                    url?: string | null
                    created_at?: string
                    updated_at?: string
                }
            }
            skills: {
                Row: {
                    id: string
                    profile_id: string | null
                    name: string
                    level: string | null
                    category: string | null
                    created_at: string
                    updated_at: string
                }
                Insert: {
                    id?: string
                    profile_id?: string | null
                    name: string
                    level?: string | null
                    category?: string | null
                    created_at?: string
                    updated_at?: string
                }
                Update: {
                    id?: string
                    profile_id?: string | null
                    name?: string
                    level?: string | null
                    category?: string | null
                    created_at?: string
                    updated_at?: string
                }
            }
        }
        Views: {
            [_ in never]: never
        }
        Functions: {
            [_ in never]: never
        }
        Enums: {
            [_ in never]: never
        }
    }
}