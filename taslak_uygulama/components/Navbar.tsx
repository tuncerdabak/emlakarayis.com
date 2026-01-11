
import React from 'react';

const Navbar: React.FC = () => {
  return (
    <nav className="bg-slate-900 text-white sticky top-0 z-50 shadow-lg">
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div className="flex justify-between h-16 items-center">
          <div className="flex items-center space-x-2">
            <div className="bg-blue-600 p-2 rounded-lg">
              <i className="fas fa-house-circle-check text-xl"></i>
            </div>
            <div>
              <span className="text-xl font-bold tracking-tight">Emlak<span className="text-blue-400">Arayış</span></span>
              <p className="text-[10px] text-slate-400 uppercase tracking-widest leading-none">Sadece Emlakçılar İçin</p>
            </div>
          </div>
          
          <div className="hidden md:flex items-center space-x-6 text-sm font-medium">
            <a href="#" className="hover:text-blue-400 transition-colors">Ana Sayfa</a>
            <a href="#feed" className="hover:text-blue-400 transition-colors">Arayışlar</a>
            <a href="#about" className="hover:text-blue-400 transition-colors">Nasıl Çalışır?</a>
            <button className="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-full text-sm font-semibold transition-all">
              Arayış Ekle
            </button>
          </div>
          
          <div className="md:hidden">
            <button className="p-2 text-slate-300">
              <i className="fas fa-bars text-xl"></i>
            </button>
          </div>
        </div>
      </div>
    </nav>
  );
};

export default Navbar;
