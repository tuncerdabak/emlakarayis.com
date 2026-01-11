
import { PropertyType, TransactionType, SearchPost } from './types';

export const MOCK_POSTS: SearchPost[] = [
  {
    id: '1',
    agentName: 'Ahmet Yılmaz',
    agencyName: 'Ekol Gayrimenkul',
    phone: '+905551234567',
    city: 'İstanbul',
    district: 'Kadıköy',
    propertyType: PropertyType.DAIRE,
    transactionType: TransactionType.SATILIK,
    maxPrice: 8500000,
    description: 'Bostancı civarında acil 3+1, krediye uygun, ara kat daire aranıyor. Müşterimiz hazır.',
    createdAt: new Date(Date.now() - 3600000).toISOString()
  },
  {
    id: '2',
    agentName: 'Ayşe Demir',
    agencyName: 'Mavi Vizyon Emlak',
    phone: '+905429876543',
    city: 'Ankara',
    district: 'Çankaya',
    propertyType: PropertyType.REZIDANS,
    transactionType: TransactionType.KIRALIK,
    maxPrice: 45000,
    description: 'Üst düzey yönetici müşterimiz için Çukurambar bölgesinde eşyalı kiralık 2+1 rezidans aranıyor.',
    createdAt: new Date(Date.now() - 7200000).toISOString()
  },
  {
    id: '3',
    agentName: 'Murat Aras',
    agencyName: 'Aras Arsa Ofisi',
    phone: '+905335554433',
    city: 'İzmir',
    district: 'Urla',
    propertyType: PropertyType.ARSA,
    transactionType: TransactionType.SATILIK,
    maxPrice: 15000000,
    description: 'Kekliktepe mevkinde villa yapımına uygun, en az 1000m2 imarlı arsa arayışımız var.',
    createdAt: new Date(Date.now() - 86400000).toISOString()
  }
];

export const CITIES = [
  'İstanbul', 'Ankara', 'İzmir', 'Bursa', 'Antalya', 'Adana', 'Konya', 'Gaziantep', 'Mersin', 'Kayseri'
];
