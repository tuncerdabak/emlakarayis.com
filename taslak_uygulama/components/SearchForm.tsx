
import React, { useState } from 'react';
import { PropertyType, TransactionType, SearchPost } from '../types';
import { CITIES } from '../constants';

interface SearchFormProps {
  onAddPost: (post: SearchPost) => void;
}

const SearchForm: React.FC<SearchFormProps> = ({ onAddPost }) => {
  const [formData, setFormData] = useState({
    agentName: '',
    agencyName: '',
    phone: '',
    city: '',
    district: '',
    propertyType: PropertyType.DAIRE,
    transactionType: TransactionType.SATILIK,
    maxPrice: '',
    description: ''
  });

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    const newPost: SearchPost = {
      id: Math.random().toString(36).substr(2, 9),
      ...formData,
      maxPrice: Number(formData.maxPrice),
      createdAt: new Date().toISOString()
    };
    onAddPost(newPost);
    setFormData({
      agentName: '',
      agencyName: '',
      phone: '',
      city: '',
      district: '',
      propertyType: PropertyType.DAIRE,
      transactionType: TransactionType.SATILIK,
      maxPrice: '',
      description: ''
    });
    alert('Arayışınız başarıyla eklendi!');
  };

  return (
    <div className="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 md:p-8">
      <h2 className="text-2xl font-bold text-slate-800 mb-6 flex items-center">
        <i className="fas fa-plus-circle text-blue-600 mr-3"></i>
        Yeni Arayış Oluştur
      </h2>
      
      <form onSubmit={handleSubmit} className="space-y-6">
        <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div className="space-y-2">
            <label className="text-sm font-semibold text-slate-700">Adınız Soyadınız</label>
            <input 
              required
              type="text" 
              className="w-full px-4 py-2 bg-slate-50 border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all outline-none"
              placeholder="Örn: Mehmet Öz"
              value={formData.agentName}
              onChange={e => setFormData({...formData, agentName: e.target.value})}
            />
          </div>
          <div className="space-y-2">
            <label className="text-sm font-semibold text-slate-700">Ofis İsmi</label>
            <input 
              required
              type="text" 
              className="w-full px-4 py-2 bg-slate-50 border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all outline-none"
              placeholder="Örn: Vizyon Gayrimenkul"
              value={formData.agencyName}
              onChange={e => setFormData({...formData, agencyName: e.target.value})}
            />
          </div>
          <div className="space-y-2">
            <label className="text-sm font-semibold text-slate-700">Telefon Numaranız</label>
            <input 
              required
              type="tel" 
              className="w-full px-4 py-2 bg-slate-50 border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all outline-none"
              placeholder="05XX XXX XX XX"
              value={formData.phone}
              onChange={e => setFormData({...formData, phone: e.target.value})}
            />
          </div>
          <div className="space-y-2">
            <label className="text-sm font-semibold text-slate-700">Maksimum Bütçe (TL)</label>
            <input 
              required
              type="number" 
              className="w-full px-4 py-2 bg-slate-50 border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all outline-none"
              placeholder="Örn: 5000000"
              value={formData.maxPrice}
              onChange={e => setFormData({...formData, maxPrice: e.target.value})}
            />
          </div>
        </div>

        <div className="grid grid-cols-1 md:grid-cols-4 gap-6">
          <div className="space-y-2">
            <label className="text-sm font-semibold text-slate-700">İl</label>
            <select 
              required
              className="w-full px-4 py-2 bg-slate-50 border border-slate-200 rounded-lg outline-none"
              value={formData.city}
              onChange={e => setFormData({...formData, city: e.target.value})}
            >
              <option value="">Seçiniz</option>
              {CITIES.map(city => <option key={city} value={city}>{city}</option>)}
            </select>
          </div>
          <div className="space-y-2">
            <label className="text-sm font-semibold text-slate-700">İlçe</label>
            <input 
              required
              type="text" 
              className="w-full px-4 py-2 bg-slate-50 border border-slate-200 rounded-lg outline-none"
              placeholder="İlçe giriniz"
              value={formData.district}
              onChange={e => setFormData({...formData, district: e.target.value})}
            />
          </div>
          <div className="space-y-2">
            <label className="text-sm font-semibold text-slate-700">Emlak Tipi</label>
            <select 
              className="w-full px-4 py-2 bg-slate-50 border border-slate-200 rounded-lg outline-none"
              value={formData.propertyType}
              onChange={e => setFormData({...formData, propertyType: e.target.value as PropertyType})}
            >
              {Object.values(PropertyType).map(type => <option key={type} value={type}>{type}</option>)}
            </select>
          </div>
          <div className="space-y-2">
            <label className="text-sm font-semibold text-slate-700">İşlem Tipi</label>
            <select 
              className="w-full px-4 py-2 bg-slate-50 border border-slate-200 rounded-lg outline-none"
              value={formData.transactionType}
              onChange={e => setFormData({...formData, transactionType: e.target.value as TransactionType})}
            >
              {Object.values(TransactionType).map(type => <option key={type} value={type}>{type}</option>)}
            </select>
          </div>
        </div>

        <div className="space-y-2">
          <label className="text-sm font-semibold text-slate-700">Arayış Detayları</label>
          <textarea 
            required
            rows={3}
            className="w-full px-4 py-2 bg-slate-50 border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none resize-none"
            placeholder="Müşterinizin tam olarak ne aradığını buraya yazın. (Metrekare, oda sayısı, konum özellikleri vb.)"
            value={formData.description}
            onChange={e => setFormData({...formData, description: e.target.value})}
          />
        </div>

        <button 
          type="submit"
          className="w-full bg-slate-900 hover:bg-black text-white py-4 rounded-xl font-bold shadow-lg shadow-slate-200 transition-all transform hover:-translate-y-1"
        >
          Arayışı Paylaş
        </button>
      </form>
    </div>
  );
};

export default SearchForm;
