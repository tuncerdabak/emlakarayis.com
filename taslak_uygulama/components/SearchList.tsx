
import React, { useState, useMemo } from 'react';
import { SearchPost, TransactionType, PropertyType } from '../types';

interface SearchListProps {
  posts: SearchPost[];
}

const getPropertyTypeColor = (type: PropertyType) => {
  switch (type) {
    case PropertyType.DAIRE: return { bg: 'bg-blue-50', border: 'border-blue-200', text: 'text-blue-800', accent: 'bg-blue-600', sub: 'text-blue-600' };
    case PropertyType.VILLA: return { bg: 'bg-purple-50', border: 'border-purple-200', text: 'text-purple-800', accent: 'bg-purple-600', sub: 'text-purple-600' };
    case PropertyType.ARSA: return { bg: 'bg-amber-50', border: 'border-amber-200', text: 'text-amber-800', accent: 'bg-amber-700', sub: 'text-amber-700' };
    case PropertyType.TICARI: return { bg: 'bg-red-50', border: 'border-red-200', text: 'text-red-800', accent: 'bg-red-600', sub: 'text-red-600' };
    case PropertyType.REZIDANS: return { bg: 'bg-cyan-50', border: 'border-cyan-200', text: 'text-cyan-800', accent: 'bg-cyan-600', sub: 'text-cyan-600' };
    case PropertyType.MUSTAKIL: return { bg: 'bg-teal-50', border: 'border-teal-200', text: 'text-teal-800', accent: 'bg-teal-600', sub: 'text-teal-600' };
    default: return { bg: 'bg-slate-50', border: 'border-slate-200', text: 'text-slate-800', accent: 'bg-slate-600', sub: 'text-slate-600' };
  }
};

const SearchList: React.FC<SearchListProps> = ({ posts }) => {
  const [filterCity, setFilterCity] = useState('');
  const [activeTab, setActiveTab] = useState<'HEPSI' | TransactionType>('HEPSI');
  
  const filteredPosts = useMemo(() => {
    return posts.filter(post => {
      const cityMatch = post.city.toLowerCase().includes(filterCity.toLowerCase());
      const typeMatch = activeTab === 'HEPSI' || post.transactionType === activeTab;
      return cityMatch && typeMatch;
    });
  }, [posts, filterCity, activeTab]);

  return (
    <div id="feed" className="space-y-6">
      <div className="bg-white p-4 md:p-6 rounded-3xl shadow-sm border border-slate-200 mb-8">
        <div className="flex flex-col gap-6">
          <div className="flex items-center justify-between">
            <h2 className="text-xl font-bold text-slate-800">Güncel Arayışlar</h2>
            <span className="text-xs font-semibold text-slate-400 bg-slate-100 px-2 py-1 rounded-full">
              {filteredPosts.length} Sonuç
            </span>
          </div>

          <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
            {/* Transaction Type Tabs */}
            <div className="flex bg-slate-100 p-1 rounded-xl">
              <button 
                onClick={() => setActiveTab('HEPSI')}
                className={`flex-1 py-2 text-xs font-bold rounded-lg transition-all ${
                  activeTab === 'HEPSI' 
                    ? 'bg-white text-slate-900 shadow-sm' 
                    : 'text-slate-500 hover:text-slate-700'
                }`}
              >
                Hepsi
              </button>
              <button 
                onClick={() => setActiveTab(TransactionType.SATILIK)}
                className={`flex-1 py-2 text-xs font-bold rounded-lg transition-all ${
                  activeTab === TransactionType.SATILIK 
                    ? 'bg-emerald-500 text-white shadow-md' 
                    : 'text-slate-500 hover:text-slate-700'
                }`}
              >
                Satılık
              </button>
              <button 
                onClick={() => setActiveTab(TransactionType.KIRALIK)}
                className={`flex-1 py-2 text-xs font-bold rounded-lg transition-all ${
                  activeTab === TransactionType.KIRALIK 
                    ? 'bg-orange-500 text-white shadow-md' 
                    : 'text-slate-500 hover:text-slate-700'
                }`}
              >
                Kiralık
              </button>
            </div>

            {/* City Search */}
            <div className="relative">
              <i className="fas fa-location-dot absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
              <input 
                type="text" 
                placeholder="İl/İlçe bazlı ara..." 
                className="w-full pl-11 pr-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm outline-none focus:ring-2 focus:ring-blue-500 transition-all"
                value={filterCity}
                onChange={e => setFilterCity(e.target.value)}
              />
            </div>
          </div>
        </div>
      </div>

      <div className="grid grid-cols-1 gap-5">
        {filteredPosts.length > 0 ? (
          filteredPosts.map(post => {
            const isRental = post.transactionType === TransactionType.KIRALIK;
            const cardTheme = isRental 
              ? 'bg-orange-50/50 border-orange-200' 
              : 'bg-emerald-50/50 border-emerald-200';
            
            const propTheme = getPropertyTypeColor(post.propertyType);
            const badgeClass = isRental ? 'bg-orange-500 text-white shadow-sm' : 'bg-emerald-500 text-white shadow-sm';
            const btnClass = isRental ? 'bg-orange-600 hover:bg-orange-700' : 'bg-emerald-600 hover:bg-emerald-700';

            return (
              <div key={post.id} className={`p-6 rounded-2xl border-2 transition-all group ${cardTheme} shadow-sm relative overflow-hidden`}>
                {/* Header Section */}
                <div className="flex flex-wrap items-start justify-between gap-4 mb-5">
                  <div className="flex items-center space-x-3">
                    <div className={`w-10 h-10 rounded-full flex items-center justify-center font-bold text-sm bg-white border ${isRental ? 'text-orange-600 border-orange-200' : 'text-emerald-600 border-emerald-200'}`}>
                      {post.agentName.charAt(0)}
                    </div>
                    <div>
                      <h3 className={`font-bold text-sm ${isRental ? 'text-orange-900' : 'text-emerald-900'}`}>{post.agentName}</h3>
                      <p className="text-[10px] text-slate-500 uppercase tracking-wider font-bold">{post.agencyName}</p>
                    </div>
                  </div>
                  <div className="flex flex-col items-end">
                    <span className={`px-4 py-1.5 rounded-lg text-xs font-black uppercase tracking-tight ${badgeClass}`}>
                      {post.transactionType}
                    </span>
                    <p className="text-[10px] text-slate-400 mt-2 font-semibold">
                      {new Date(post.createdAt).toLocaleTimeString('tr-TR', { hour: '2-digit', minute: '2-digit' })} • {new Date(post.createdAt).toLocaleDateString('tr-TR')}
                    </p>
                  </div>
                </div>

                {/* Property Type and Details Section - THEMED BY PROPERTY TYPE */}
                <div className={`p-4 rounded-xl border-2 mb-4 transition-colors ${propTheme.bg} ${propTheme.border}`}>
                  <div className="flex flex-wrap items-center gap-2 mb-3">
                    <span className={`text-[11px] px-2.5 py-1 rounded-full font-black uppercase tracking-wider text-white shadow-sm ${propTheme.accent}`}>
                      {post.propertyType}
                    </span>
                    <span className={`text-[11px] px-2.5 py-1 rounded-full font-bold bg-white border ${propTheme.border} ${propTheme.sub}`}>
                      <i className="fas fa-map-marker-alt mr-1"></i> {post.city} / {post.district}
                    </span>
                    <div className="ml-auto bg-slate-900 text-white px-3 py-1 rounded-lg shadow-sm">
                       <span className="text-[10px] opacity-70 mr-1">Bütçe:</span>
                       <span className="text-xs font-black">{new Intl.NumberFormat('tr-TR').format(post.maxPrice)} TL</span>
                    </div>
                  </div>
                  <div className={`text-sm leading-relaxed font-semibold italic ${propTheme.text}`}>
                    "{post.description}"
                  </div>
                </div>

                {/* Actions Section */}
                <div className="flex items-center justify-between">
                  <div className="flex space-x-2 w-full sm:w-auto">
                    <a 
                      href={`tel:${post.phone}`}
                      className={`flex-1 sm:flex-none justify-center text-white px-6 py-2.5 rounded-xl text-xs font-bold transition-all flex items-center shadow-lg shadow-black/5 ${btnClass}`}
                    >
                      <i className="fas fa-phone mr-2"></i> Ara
                    </a>
                    <a 
                      href={`https://wa.me/${post.phone.replace(/\+/g, '')}?text=Merhaba, EmlakArayış üzerinden paylaştığınız ${post.district} arayışınız için ulaşıyorum.`}
                      target="_blank"
                      rel="noopener noreferrer"
                      className="flex-1 sm:flex-none justify-center bg-white text-slate-800 border border-slate-200 hover:border-emerald-500 hover:text-emerald-600 px-6 py-2.5 rounded-xl text-xs font-bold transition-all flex items-center shadow-sm"
                    >
                      <i className="fab fa-whatsapp mr-2 text-emerald-500"></i> WhatsApp
                    </a>
                  </div>
                  <div className="hidden sm:flex items-center space-x-4 text-slate-300">
                    <button className="hover:text-red-500 transition-colors"><i className="far fa-heart"></i></button>
                    <button className="hover:text-blue-500 transition-colors"><i className="fas fa-share-nodes"></i></button>
                  </div>
                </div>
              </div>
            );
          })
        ) : (
          <div className="text-center py-20 bg-white rounded-3xl border-2 border-dashed border-slate-100">
            <div className="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-4">
              <i className="fas fa-wind text-slate-300 text-2xl"></i>
            </div>
            <p className="text-slate-400 font-bold text-sm">Eşleşen arayış bulunamadı.</p>
            <p className="text-slate-300 text-xs mt-1">Filtreleri değiştirerek tekrar deneyebilirsiniz.</p>
          </div>
        )}
      </div>
    </div>
  );
};

export default SearchList;
