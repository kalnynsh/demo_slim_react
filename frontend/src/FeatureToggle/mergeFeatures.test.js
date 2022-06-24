import mergeFeatures from './mergeFeatures'

test('merges feature structs and arrays', () => {
  const featureStructure = {
    first: true,
    second: false,
    third: true,
    fourth: false,
    fifth: true,
  }

  const featureTwo = ['second', '!third']

  const featureThree = {
    fourth: true,
    fifth: false,
  }

  const features = mergeFeatures(featureStructure, featureTwo, featureThree)

  expect(features).toEqual(['first', 'fourth', 'second'])
})
