
import React, { useState, useEffect } from 'react';
import Navbar from './components/Navbar';
import SearchForm from './components/SearchForm';
import SearchList from './components/SearchList';
import AIChat from './components/AIChat';
import { SearchPost } from './types';
import { MOCK_POSTS } from './constants';

const App: React.FC = () => {
  const [posts, setPosts] = useState<SearchPost[]>(MOCK_POSTS);

  const handleAddPost = (newPost: SearchPost) => {
    setPosts([newPost, ...posts]);
  };

  return (
    <div className="min-h-screen flex flex-col">
      <Navbar />
      
      {/* Hero Section */}
      <header className="bg-slate-900 text-white pt-20 pb-32 relative overflow-hidden">
        <div className="absolute inset-0 opacity-10">
          <div className="absolute -left-20 -top-20 w-96 h-96 bg-blue-600 rounded-full blur-[100px]"></div>
          <div className="absolute -right-20 -bottom-20 w-96 h-96 bg-indigo-600 rounded-full blur-[100px]"></div>
        </div>
        
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 text-center">
          <h1 className="text-4xl md:text-6xl font-extrabold tracking-tight mb-6 leading-tight">
            Müşteriniz Hazır, <br />
            <span className="text-blue-500">Portföyü Meslektaşınızda Bulun.</span>
          </h1>
          <p className="text-lg md:text-xl text-slate-300 max-w-2xl mx-auto mb-10 leading-relaxed">
            İlan girmekle vakit kaybetmeyin. Müşteri taleplerini paylaşın, binlerce meslektaşınızın portföyü arasından aradığınız mülke saniyeler içinde ulaşın.
          </p>
          <div className="flex flex-col sm:flex-row items-center justify-center gap-4">
            <a href="#post-section" className="w-full sm:w-auto bg-blue-600 hover:bg-blue-700 text-white px-8 py-4 rounded-xl font-bold text-lg shadow-xl shadow-blue-500/20 transition-all transform hover:-translate-y-1">
              Arayışını Paylaş
            </a>
            <a href="#feed" className="w-full sm:w-auto bg-slate-800 hover:bg-slate-700 text-white px-8 py-4 rounded-xl font-bold text-lg border border-slate-700 transition-all">
              Arayışları İncele
            </a>
          </div>
        </div>
      </header>

      {/* Main Content */}
      <main className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 -mt-16 pb-20 relative z-20">
        <div className="grid grid-cols-1 lg:grid-cols-12 gap-8">
          
          {/* Left Column: Form Section */}
          <div id="post-section" className="lg:col-span-5 space-y-8">
            <SearchForm onAddPost={handleAddPost} />
            
            <div id="about" className="bg-blue-600 text-white p-8 rounded-3xl shadow-xl">
              <h3 className="text-xl font-bold mb-4 flex items-center">
                <i className="fas fa-bolt mr-2"></i> Nasıl Çalışır?
              </h3>
              <ul className="space-y-4">
                <li className="flex items-start">
                  <span className="bg-blue-500 text-white w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold mt-1 mr-3 flex-shrink-0">1</span>
                  <p className="text-sm text-blue-50">Müşterinizin kriterlerini ve bütçesini form aracılığıyla paylaşın.</p>
                </li>
                <li className="flex items-start">
                  <span className="bg-blue-500 text-white w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold mt-1 mr-3 flex-shrink-0">2</span>
                  <p className="text-sm text-blue-50">Diğer emlakçılar akıştaki arayışları görerek size ulaşsın.</p>
                </li>
                <li className="flex items-start">
                  <span className="bg-blue-500 text-white w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold mt-1 mr-3 flex-shrink-0">3</span>
                  <p className="text-sm text-blue-50">İlan girme derdi olmadan portföy paylaşımı yapın ve iş birliğini tamamlayın.</p>
                </li>
              </ul>
            </div>
          </div>

          {/* Right Column: List Section */}
          <div className="lg:col-span-7">
            <SearchList posts={posts} />
          </div>
          
        </div>
      </main>

      <footer className="bg-white border-t border-slate-200 py-12">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col md:flex-row justify-between items-center gap-6">
          <div className="flex items-center space-x-2">
            <div className="bg-slate-900 p-2 rounded-lg">
              <i className="fas fa-house-circle-check text-white"></i>
            </div>
            <span className="text-lg font-bold">EmlakArayış</span>
          </div>
          <p className="text-slate-400 text-sm">
            © 2024 emlakarayis.com - Sadece emlakçılar için geliştirilmiştir.
          </p>
          <div className="flex space-x-6">
            <a href="#" className="text-slate-400 hover:text-blue-600 transition-colors"><i className="fab fa-instagram text-xl"></i></a>
            <a href="#" className="text-slate-400 hover:text-blue-600 transition-colors"><i className="fab fa-linkedin text-xl"></i></a>
            <a href="#" className="text-slate-400 hover:text-blue-600 transition-colors"><i className="fab fa-twitter text-xl"></i></a>
          </div>
        </div>
      </footer>

      {/* Floating Components */}
      <AIChat />
    </div>
  );
};

export default App;
