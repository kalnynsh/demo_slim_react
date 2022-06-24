import { useContext } from 'react'
import FeaturesContext from './FeaturesContext'

export default function useFeatures() {
  return useContext(FeaturesContext)
}
