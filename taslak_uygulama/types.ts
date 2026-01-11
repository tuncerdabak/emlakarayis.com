
export enum PropertyType {
  DAIRE = 'Daire',
  VILLA = 'Villa',
  ARSA = 'Arsa',
  TICARI = 'Ticari',
  REZIDANS = 'Rezidans',
  MUSTAKIL = 'Müstakil Ev'
}

export enum TransactionType {
  SATILIK = 'Satılık',
  KIRALIK = 'Kiralık'
}

export interface SearchPost {
  id: string;
  agentName: string;
  agencyName: string;
  phone: string;
  city: string;
  district: string;
  propertyType: PropertyType;
  transactionType: TransactionType;
  minPrice?: number;
  maxPrice: number;
  description: string;
  createdAt: string;
}

export interface ChatMessage {
  role: 'user' | 'model';
  text: string;
  timestamp: Date;
}
