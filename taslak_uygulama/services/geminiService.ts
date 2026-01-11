
import { GoogleGenAI } from "@google/genai";

const SYSTEM_INSTRUCTION = `Sen EmlakArayis.com platformunun yapay zeka asistanısın. 
Bu platform emlakçıların (gayrimenkul danışmanlarının) birbirleriyle ilan girmeden, sadece müşteri taleplerini paylaşarak yardımlaştığı bir ağdır.
Görevlerin:
1. Emlakçılara sektörel konularda (tapu süreçleri, ekspertiz, kredi, vergilendirme) bilgi ver.
2. Platform kullanımı hakkında rehberlik et.
3. Emlak danışmanlarına pazarlama ve müşteri ilişkileri tavsiyeleri sun.
4. Yanıtlarını profesyonel, nazik ve Türkçe emlak terminolojisine uygun şekilde ver.
5. Kullanıcının emlakçı olduğunu varsayarak konuş.
Platformun temel amacı: İlan girmeden, sadece arayış paylaşarak meslektaşlar arası hızlı portföy paylaşımı sağlamaktır.`;

export const getGeminiResponse = async (prompt: string) => {
  const ai = new GoogleGenAI({ apiKey: process.env.API_KEY || '' });
  
  try {
    const response = await ai.models.generateContent({
      model: "gemini-3-pro-preview",
      contents: prompt,
      config: {
        systemInstruction: SYSTEM_INSTRUCTION,
        temperature: 0.7,
        topP: 0.8,
        topK: 40
      }
    });
    
    return response.text;
  } catch (error) {
    console.error("Gemini API Error:", error);
    return "Üzgünüm, şu an yanıt veremiyorum. Lütfen daha sonra tekrar deneyiniz.";
  }
};
